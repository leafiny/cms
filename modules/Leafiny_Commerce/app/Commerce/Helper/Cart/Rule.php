<?php
/**
 * This file is part of Leafiny.
 *
 * Copyright (C) Magentix SARL
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * Class Commerce_Helper_Cart_Rule
 */
class Commerce_Helper_Cart_Rule extends Core_Helper
{
    /**
     * Retrieve active sale rules
     *
     * @param Leafiny_Object|null $sale
     * @param array|null          $types
     *
     * @return Leafiny_Object[]
     */
    public function getCartRules(?Leafiny_Object $sale = null, ?array $types = null): array
    {
        $result = [];

        try {
            if ($sale === null) {
                $sale = $this->getSale($this->getSaleId());
            }

            $rulesIds = $sale->getData('rule_ids');

            if (!$rulesIds) {
                return $result;
            }

            $filters = [
                [
                    'column'   => 'rule_id',
                    'value'    => explode(',', $rulesIds),
                    'operator' => 'IN'
                ]
            ];
            if (!empty($types)) {
                $filters[] = [
                    'column'   => 'type',
                    'value'    => $types,
                    'operator' => 'IN'
                ];
            }

            $orders = [
                [
                    'order' => 'priority',
                    'dir'   => 'ASC'
                ]
            ];

            /** @var Commerce_Model_Cart_Rule $ruleModel */
            $ruleModel = App::getObject('model', 'cart_rule');
            $rules = $ruleModel->getList($filters, $orders);

            if (empty($rules)) {
                return $result;
            }

            $saleId = (int)$sale->getData('sale_id');

            $items = $this->getItems($saleId);

            foreach ($rules as $rule) {
                App::dispatchEvent('cart_rule_candidate', ['rule' => $rule, 'sale_id' => $saleId, 'items' => $items]);

                if (!$rule->getData('status') ||
                    $ruleModel->isExpired($rule->getData('expire')) ||
                    !$this->isValid($sale, $items, $rule))
                {
                    if (!$sale->getData('_keep_rules')) {
                        $this->removeCartRule((int)$rule->getData('rule_id'), $saleId);
                    }
                    continue;
                }

                $priority = (int)$rule->getData('priority');
                while(isset($result[$priority])) {
                    $priority++;
                }
                $result[$priority] = $rule;

                if ($rule->getData('stop_rules_processing')) {
                    break;
                }
            }

            App::dispatchEvent('cart_rules_to_apply', ['rules' => $result, 'sale_id' => $saleId]);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $result;
    }

    /**
     * Check conditions
     *
     * @param Leafiny_Object   $sale
     * @param Leafiny_Object[] $items
     * @param Leafiny_Object   $rule
     *
     * @return bool
     */
    public function isValid(Leafiny_Object $sale, array $items, Leafiny_Object $rule): bool
    {
        if (!$rule->getData('conditions')) {
            return true;
        }

        $conditions = json_decode($rule->getData('conditions'), true);
        if (!$conditions) {
            return true;
        }

        foreach ($conditions as $condition) {
            $data = new Leafiny_Object($condition);
            if (!$data->getData('field') && $data->getData('operator')) {
                continue;
            }

            $field = explode(':', $data->getData('field'));
            if (count($field) !== 2) {
                continue;
            }

            $values = $data->getData('values') ?: [];
            $operator = $data->getData('operator');

            $toValidate = new Leafiny_Object(
                [
                    'rule'      => $rule,
                    'sale'      => $sale,
                    'items'     => $items,
                    'condition' => $data,
                    'operator'  => $operator,
                    'values'    => $values,
                    'type'      => $field[0],
                    'key'       => $field[1],
                    'is_valid'  => true,
                ]
            );

            App::dispatchEvent('cart_rule_condition_custom_validator', ['to_validate' => $toValidate]);

            if (!$toValidate->getData('is_valid')) {
                return false;
            }

            if ($field[0] === 'sale') {
                if (!$this->validateCondition($sale->getData($field[1]), $values, $operator)) {
                    return false;
                }
            }
            if (in_array($field[0], ['item', 'product'])) {
                if ($operator === 'ne') {
                    foreach ($items as $item) {
                        $object = $field[0] === 'product' ? $item->getData('product') : $item;
                        // If at least one item is found the condition is false, we return false
                        if (!$this->validateCondition($object->getData($field[1]), $values, $operator)) {
                            return false;
                        }
                    }
                } else {
                    $oneLeastIsValid = false;
                    foreach ($items as $item) {
                        $object = $field[0] === 'product' ? $item->getData('product') : $item;
                        if ($this->validateCondition($object->getData($field[1]), $values, $operator)) {
                            $oneLeastIsValid = true;
                        }
                    }
                    // If no item was found, we return false
                    if (!$oneLeastIsValid) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Add cart rule
     *
     * @param int                 $ruleId
     * @param Leafiny_Object|null $coupon
     * @param int|null            $saleId
     *
     * @return bool
     */
    public function addCartRule(int $ruleId, ?Leafiny_Object $coupon, ?int $saleId = null): bool
    {
        try {
            if ($saleId === null) {
                $saleId = $this->getSaleId();
            }
            if ($saleId === null) {
                return false;
            }

            /** @var Commerce_Model_Cart_Rule $ruleModel */
            $ruleModel = App::getSingleton('model', 'cart_rule');
            $rule = $ruleModel->get($ruleId);

            if (!$rule->getData('rule_id')) {
                return false;
            }
            if (!$rule->getData('status') || $ruleModel->isExpired($rule->getData('expire'))) {
                return false;
            }

            if ($coupon) {
                if ((int)$coupon->getData('rule_id') !== $ruleId) {
                    return false;
                }
                if (!$rule->getData('has_coupon')) {
                    return false;
                }
                if (!$coupon->getData('status') || $coupon->getData('used') >= $coupon->getData('limit')) {
                    return false;
                }
            }

            $sale  = $this->getSale($saleId);
            $items = $this->getItems($saleId);

            if (!$this->isValid($sale, $items, $rule)) {
                return false;
            }

            $ruleIds = explode(',', (string) $sale->getData('rule_ids'));
            $ruleIds[] = $ruleId;

            $ruleIds = array_filter(array_unique($ruleIds));

            $data = [
                'sale_id'  => $saleId,
                'rule_ids' => join(',', $ruleIds),
            ];
            if ($coupon) {
                $data['coupon_code']    = $coupon->getData('code');
                $data['coupon_id']      = $coupon->getData('coupon_id');
                $data['coupon_rule_id'] = $coupon->getData('rule_id');
            }

            $update = new Leafiny_Object($data);

            App::dispatchEvent(
                'cart_rule_add_to_sale_before',
                [
                    'rule'   => $rule,
                    'coupon' => $coupon,
                    'sale'   => $sale,
                    'update' => $update,
                ]
            );

            $result = (bool)$this->getSaleObject()->save($update);

            App::dispatchEvent(
                'cart_rule_add_to_sale_after',
                [
                    'rule'   => $rule,
                    'coupon' => $coupon,
                    'sale'   => $sale
                ]
            );

            return $result;
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return false;
    }

    /**
     * Remove cart rule
     *
     * @param int|null $ruleId
     * @param int|null $saleId
     *
     * @return bool
     */
    public function removeCartRule(?int $ruleId = null, ?int $saleId = null): bool
    {
        try {
            if ($saleId === null) {
                $saleId = $this->getSaleId();
            }
            if ($saleId === null) {
                return false;
            }

            $sale = $this->getSale($saleId);
            $ruleIds = explode(',', (string)$sale->getData('rule_ids'));
            if ($ruleId === null) {
                $ruleIds = [];
            }
            if ($ruleId && !empty($ruleIds)) {
                foreach ($ruleIds as $key => $value) {
                    if ($ruleId !== (int)$value) {
                        continue;
                    }
                    unset($ruleIds[$key]);
                }
            }

            $ruleIds = array_filter(array_unique($ruleIds));

            $data = [
                'sale_id'  => (int)$sale->getData('sale_id'),
                'rule_ids' => join(',', $ruleIds),
            ];

            if (!$ruleId || (int)$sale->getData('coupon_rule_id') === $ruleId) {
                $data['coupon_code'] = null;
                $data['coupon_id'] = null;
                $data['coupon_rule_id'] = null;
            }

            $update = new Leafiny_Object($data);

            App::dispatchEvent(
                'cart_rule_remove_from_sale_before',
                [
                    'rule_id' => $ruleId,
                    'sale'    => $sale,
                    'update'  => $update,
                ]
            );

            $result = (bool)$this->getSaleObject()->save($update);

            App::dispatchEvent(
                'cart_rule_remove_from_sale_after',
                [
                    'rule_id' => $ruleId,
                    'sale'    => $sale,
                ]
            );

            return $result;
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return false;
    }

    /**
     * Apply cart rules without coupons
     *
     * @param int|null $saleId
     * @return bool
     */
    public function applyNoCouponCartRules(?int $saleId = null): bool
    {
        try {
            if ($saleId === null) {
                $saleId = $this->getSaleId(true);
            }
            if ($saleId === null) {
                return false;
            }

            /** @var Commerce_Model_Cart_Rule $ruleModel */
            $ruleModel = App::getSingleton('model', 'cart_rule');
            $filters = [
                [
                    'column' => 'status',
                    'value'  => 1,
                ],
                [
                    'column' => 'has_coupon',
                    'value'  => 0,
                ],
            ];
            $rules = $ruleModel->getList($filters);
            foreach ($rules as $rule) {
                $this->addCartRule((int)$rule->getData('rule_id'), null, $saleId);
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return false;
    }

    /**
     * Retrieve shipping discount rate
     *
     * @param Leafiny_Object|null $sale
     *
     * @return float
     */
    public function getShippingDiscountRate(?Leafiny_Object $sale = null): float
    {
        $rate = 1;

        try {
            if ($sale === null) {
                $sale = $this->getSale($this->getSaleId());
            }

            $rules = $this->getCartRules($sale, [Commerce_Model_Cart_Rule::TYPE_PERCENT_SHIPPING]);

            foreach ($rules as $rule) {
                $rule->setData('discount_shipping_rate', 1 - (float)$rule->getData('discount') / 100);

                App::dispatchEvent('cart_rule_discount_shipping_rate', ['sale' => $sale, 'rule' => $rule]);

                $rate = (float)$rule->getData('discount_shipping_rate');
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $rate;
    }

    /**
     * Add gift if needed
     *
     * @param int|null $saleId
     */
    public function addFreeGift(?int $saleId = null): void
    {
        try {
            if ($saleId === null) {
                $saleId = $this->getSaleId();
            }
            if ($saleId === null) {
                return;
            }

            /** @var Commerce_Model_Sale_Item $saleItemModel */
            $saleItemModel = App::getSingleton('model', 'sale_item');

            $sale  = $this->getSale($saleId);
            $rules = $this->getCartRules($sale, [Commerce_Model_Cart_Rule::TYPE_FREE_GIFT]);

            $current = [];
            foreach ($rules as $rule) {
                $current[] = (int)$rule->getData('rule_id');
            }

            $items = $this->getItems($saleId);
            foreach ($items as $item) {
                $ruleId = $this->getFreeGiftRuleId($item);
                if (!$ruleId) {
                    continue;
                }
                if (in_array($ruleId, $current)) {
                    continue;
                }
                $saleItemModel->delete((int)$item->getData('item_id'));
            }

            if (empty($rules)) {
                return;
            }

            /** @var Catalog_Model_Product $catalogModel */
            $catalogModel = App::getSingleton('model', 'catalog_product');
            /** @var Commerce_Helper_Cart $cartHelper */
            $cartHelper = App::getSingleton('helper', 'cart');

            foreach ($rules as $rule) {
                $sku = $rule->getData('option');
                if (!$sku) {
                    continue;
                }
                $product = $catalogModel->get($sku, 'sku');
                if (!$product->getData('product_id')) {
                    continue;
                }

                $item = $saleItemModel->getItem($saleId, $product->getData('product_id'), 'product_id');

                $item->addData(
                    [
                        'sale_id'              => $saleId,
                        'qty'                  => 1,
                        'options'              => json_encode(['free_gift' => (int)$rule->getData('rule_id')]),
                        'custom_incl_tax_unit' => 0,
                        'custom_excl_tax_unit' => 0,
                    ]
                );

                $cartHelper->updateItem($item, $product);
            }

        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }

    /**
     * Refresh items discount if needed
     *
     * @param int|null $saleId
     */
    public function refreshItemsDiscount(?int $saleId = null): void
    {
        try {
            if ($saleId === null) {
                $saleId = $this->getSaleId();
            }
            if ($saleId === null) {
                return;
            }

            $sale = $this->getSale($saleId);

            /** @var Commerce_Model_Sale_Item $itemModel */
            $itemModel = App::getObject('model', 'sale_item');
            $items = $itemModel->getItems($saleId);

            foreach ($items as $item) {
                $data = [
                    'discount_excl_tax_unit' => 0,
                    'discount_incl_tax_unit' => 0,
                    'discount_excl_tax_row'  => 0,
                    'discount_incl_tax_row'  => 0,
                    'discount_tax_unit'      => 0,
                    'discount_tax_row'       => 0,
                ];
                $item->addData($data);
                $itemModel->save($item);
            }

            $rules = $this->getCartRules($sale, $this->getCartRuleTypeCartDiscount());

            foreach ($rules as $rule) {
                App::dispatchEvent('cart_rule_discount_refresh_before', ['sale' => $sale, 'rule' => $rule]);

                $discount = (float)$rule->getData('discount');

                foreach ($items as $item) {
                    $inclTaxDiscount = 0;
                    $exclTaxDiscount = 0;

                    if ($rule->getData('type') === Commerce_Model_Cart_Rule::TYPE_PERCENT_SUBTOTAL) {
                        $inclTaxDiscount = $item->getData('incl_tax_unit') * $discount / 100;
                        $exclTaxDiscount = $item->getData('excl_tax_unit') * $discount / 100;
                    }
                    if ($rule->getData('type') === Commerce_Model_Cart_Rule::TYPE_AMOUNT_PER_PRODUCT) {
                        $inclTaxDiscount = $discount;
                        $exclTaxDiscount = $discount / (($item->getData('tax_percent') / 100) + 1);
                    }

                    $item->setData(
                        'discount_amount',
                        new Leafiny_Object(
                            [
                                'discount_incl_tax' => $inclTaxDiscount,
                                'discount_excl_tax' => $exclTaxDiscount,
                            ]
                        )
                    );

                    App::dispatchEvent(
                        'cart_rule_item_discount_amount',
                        [
                            'sale' => $sale,
                            'item' => $item,
                            'rule' => $rule
                        ]
                    );

                    $inclTaxDiscount = $item->getData('discount_amount')->getData('discount_incl_tax');
                    $exclTaxDiscount = $item->getData('discount_amount')->getData('discount_excl_tax');

                    $this->setItemDiscountAmount(
                        $item,
                        'discount_excl_tax_unit',
                        'excl_tax_unit',
                        $exclTaxDiscount
                    );
                    $this->setItemDiscountAmount(
                        $item,
                        'discount_incl_tax_unit',
                        'incl_tax_unit',
                        $inclTaxDiscount
                    );
                    $this->setItemDiscountAmount(
                        $item,
                        'discount_excl_tax_row',
                        'excl_tax_row',
                        $exclTaxDiscount * $item->getData('qty')
                    );
                    $this->setItemDiscountAmount(
                        $item,
                        'discount_incl_tax_row',
                        'incl_tax_row',
                        $inclTaxDiscount * $item->getData('qty')
                    );
                    $this->setItemDiscountAmount(
                        $item,
                        'discount_tax_unit',
                        'tax_amount_unit',
                        $inclTaxDiscount - $exclTaxDiscount
                    );
                    $this->setItemDiscountAmount(
                        $item,
                        'discount_tax_row',
                        'tax_amount_row',
                        ($inclTaxDiscount - $exclTaxDiscount) * $item->getData('qty')
                    );

                    $itemModel->save($item);
                }

                App::dispatchEvent('cart_rule_discount_refresh_after', ['sale' => $sale, 'rule' => $rule]);
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }

    /**
     * Item is a gift
     *
     * @param Leafiny_Object $item
     *
     * @return int
     */
    public function getFreeGiftRuleId(Leafiny_Object $item): ?int
    {
        $options = $item->getData('options');
        if (!$options) {
            return null;
        }
        $options = json_decode($options, true);
        if (!isset($options['free_gift'])) {
            return null;
        }

        return (int)$options['free_gift'];
    }

    /**
     * Set item discount amount. Avoid the amount exceed the referer.
     *
     * @param Leafiny_Object $item
     * @param string         $column
     * @param string         $referer
     * @param float          $amount
     *
     * @return void
     */
    protected function setItemDiscountAmount(Leafiny_Object $item, string $column, string $referer, float $amount): void
    {
        $value  = $item->getData($referer);
        $amount = $item->getData($column) + $amount;

        if ($amount > $value) {
            $amount = $value;
        }

        $item->setData($column, $amount);
    }


    /**
     * Retrieve sale from id
     *
     * @param int $saleId
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    protected function getSale(int $saleId): Leafiny_Object
    {
        /** @var Commerce_Helper_Cart $cartHelper */
        $cartHelper = App::getSingleton('helper', 'cart');

        return $cartHelper->getSale($saleId);
    }

    /**
     * Retrieve sale from id
     *
     * @param int $saleId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    protected function getItems(int $saleId): array
    {
        /** @var Commerce_Helper_Cart $cartHelper */
        $cartHelper = App::getSingleton('helper', 'cart');

        return $cartHelper->getItems($saleId);
    }

    /**
     * Retrieve sale from id
     *
     * @return int
     * @throws Exception
     */
    protected function getSaleId(): int
    {
        /** @var Commerce_Helper_Cart $cartHelper */
        $cartHelper = App::getSingleton('helper', 'cart');

        return $cartHelper->getCurrentId(true);
    }

    /**
     * Retrieve Sale Object
     *
     * @return Commerce_Model_Sale
     */
    protected function getSaleObject(): Commerce_Model_Sale
    {
        return App::getSingleton('model', 'sale');
    }

    /**
     * Retrieve allowed cart rules
     *
     * @return array
     */
    public function getCartRuleAllowedTypes(): array
    {
        return array_merge_recursive(
            [
                'discount' => [
                    Commerce_Model_Cart_Rule::TYPE_PERCENT_SUBTOTAL   => 'Subtotal Discount (%)',
                    Commerce_Model_Cart_Rule::TYPE_PERCENT_SHIPPING   => 'Shipping Discount (%)',
                    Commerce_Model_Cart_Rule::TYPE_AMOUNT_PER_PRODUCT => 'Discount per product (amount)',
                ],
                'option' => [
                    Commerce_Model_Cart_Rule::TYPE_FREE_GIFT => 'Free Gift',
                ],
            ],
            $this->getCustom('cart_rule_allowed_types') ?? []
        );
    }

    /**
     * Retrieve cart rule type discount
     *
     * @return array
     */
    public function getCartRuleTypeCartDiscount(): array
    {
        return array_merge(
            [
                Commerce_Model_Cart_Rule::TYPE_PERCENT_SUBTOTAL,
                Commerce_Model_Cart_Rule::TYPE_AMOUNT_PER_PRODUCT,
            ],
            $this->getCustom('cart_rule_type_cart_discount') ?? []
        );
    }

    /**
     * Retrieve conditions operators
     *
     * @return string[]
     */
    public function getConditionOperators(): array
    {
        return array_merge(
            [
                'eq' => '=',
                'ne' => '!=',
                'lt' => '<',
                'le' => '<=',
                'gt' => '>',
                'ge' => '>=',
            ],
            $this->getCustom('condition_operators') ?? []
        );
    }

    /**
     * Retrieve conditions data
     *
     * @return string[]
     */
    public function getConditionFields(): array
    {
        return array_merge(
            [
                'sale:incl_tax_subtotal' => 'Cart - Subtotal Incl. Tax',
                'sale:excl_tax_subtotal' => 'Cart - Subtotal Excl. Tax',
                'sale:shipping_method'   => 'Cart - Shipping Method',
                'sale:total_weight'      => 'Cart - Total Weight',
                'sale:language'          => 'Store - Language',
                'item:product_sku'       => 'Product - SKU',
                'product:category_ids'   => 'Product - Category ID',
                'sale:incl_tax_subtotal_with_discount' =>
                    'Cart - Subtotal Incl. Tax with discount (Only works with Shipping Discount rule)',
                'sale:excl_tax_subtotal_with_discount' =>
                    'Cart - Subtotal Excl. Tax with discount (Only works with Shipping Discount rule)',
            ],
            $this->getCustom('condition_fields') ?? []
        );
    }

    /**
     * Check if the condition is valid
     *
     * @param mixed   $value
     * @param mixed[] $toFind
     * @param string  $operator
     *
     * @return bool
     */
    protected function validateCondition($value, array $toFind, string $operator): bool
    {
        if ($operator === 'ne') {
            foreach ($toFind as $toCompare) {
                if (!$this->compare($value, $toCompare, $operator)) {
                    return false;
                }
            }
            return true;
        }

        foreach ($toFind as $toCompare) {
            if ($this->compare($value, $toCompare, $operator)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Compare 2 values
     *
     * @param mixed $value1
     * @param mixed $value2
     * @param string $operator
     *
     * @return bool
     */
    protected function compare($value1, $value2, string $operator): bool
    {
        $compare = function ($value1, $value2, string $operator): bool
        {
            switch ($operator) {
                case 'eq':
                    return $value1 == $value2;
                case 'ne':
                    return $value1 != $value2;
                case 'lt':
                    return $value1 < $value2;
                case 'le':
                    return $value1 <= $value2;
                case 'gt':
                    return $value1 > $value2;
                case 'ge':
                    return $value1 >= $value2;
            }

            $data = new Leafiny_Object(
                [
                    'value1'   => $value1,
                    'value2'   => $value2,
                    'operator' => $operator,
                    'result'   => false,
                ]
            );

            App::dispatchEvent('cart_rule_compare', ['compare' => $data]);

            return (bool)$data->getData('result');
        };

        if(is_array($value1)) {
            return !empty(array_filter($value1, function($value) use($value2, $operator, $compare) {
                return $compare($value, $value2, $operator);
            }));
        }

        return $compare($value1, $value2, $operator);
    }
}
