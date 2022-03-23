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
     * @param int|null $saleId
     *
     * @return Leafiny_Object[]
     */
    public function getCartRules(?int $saleId = null): array
    {
        $result = [];

        try {
            if ($saleId === null) {
                $saleId = $this->getSaleId();
            }
            if ($saleId === null) {
                return $result;
            }

            $sale = $this->getSale($saleId);
            $rulesIds = $sale->getData('rule_ids');

            if (!$rulesIds) {
                return $result;
            }

            /** @var Commerce_Model_Cart_Rule $ruleModel */
            $ruleModel = App::getObject('model', 'cart_rule');

            $filters = [
                [
                    'column'   => 'rule_id',
                    'value'    => explode(',', $rulesIds),
                    'operator' => 'IN'
                ]
            ];
            $orders = [
                [
                    'order' => 'priority',
                    'dir'   => 'ASC'
                ]
            ];

            $rules = $ruleModel->getList($filters, $orders);

            foreach ($rules as $rule) {
                App::dispatchEvent('cart_rule_candidate', ['rule' => $rule, 'sale' => $sale]);

                if (!$rule->getData('discount')) {
                    continue;
                }
                if (!$rule->getData('status') || $ruleModel->isExpired($rule->getData('expire'))) {
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

            App::dispatchEvent('cart_rules_to_apply', ['rules' => $result, 'sale' => $sale]);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $result;
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

            $sale = $this->getSale($saleId);
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
            $ruleIds = explode(',', (string) $sale->getData('rule_ids'));
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
                'sale_id'  => $saleId,
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
     * @param int|null $saleId
     *
     * @return float
     */
    public function getShippingDiscountRate(?int $saleId = null): float
    {
        $rate = 1;

        try {
            if ($saleId === null) {
                $saleId = $this->getSaleId();
            }
            if ($saleId === null) {
                return $rate;
            }

            $sale = $this->getSale($saleId);

            $rules = $this->getCartRules((int)$sale->getData('sale_id'));

            foreach ($rules as $rule) {
                if ($rule->getData('type') !== Commerce_Model_Cart_Rule::TYPE_PERCENT_SHIPPING) {
                    continue;
                }

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

            $rules = $this->getCartRules((int)$sale->getData('sale_id'));

            foreach ($rules as $rule) {
                App::dispatchEvent('cart_rule_discount_refresh_before', ['sale' => $sale, 'rule' => $rule]);

                $allowedTypes = $this->getCartRuleAllowedTypes();
                if (!isset($allowedTypes[$rule->getData('type')])) {
                    return;
                }

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
     * Retrieve allowed cart rules
     *
     * @return array
     */
    public function getCartRuleAllowedTypes(): array
    {
        return $this->getCustom('cart_rule_allowed_types') ?: [];
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
}
