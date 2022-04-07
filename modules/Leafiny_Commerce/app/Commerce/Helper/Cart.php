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
 * Class Commerce_Helper_Cart
 */
class Commerce_Helper_Cart extends Core_Helper
{
    /**
     * Retrieve store currency
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->getCustom('currency');
    }

    /**
     * Retrieve available payment methods
     *
     * @return string[]
     */
    public function getPaymentMethods(): array
    {
        return $this->getCustom('payment_methods') ?: [];
    }

    /**
     * Retrieve current sale identifier
     *
     * @param bool $init
     *
     * @return int|null
     * @throws Exception
     */
    public function getCurrentId(bool $init = false): ?int
    {
        $session = App::getSession(Core_Template_Abstract::CONTEXT_DEFAULT);

        if (!$session) {
            return null;
        }

        $saleId = $session->get('sale_id');

        if ($saleId && !$this->isCartExist($saleId)) {
            $saleId = null;
        }

        if (!$saleId && $init) {
            $saleId = $this->initSale();
            $session->set('sale_id', $saleId);
        }

        return $saleId;
    }

    /**
     * Add product to current sale
     *
     * @param Leafiny_Object $product
     * @param int            $qty
     * @param int|null       $saleId
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function addProduct(Leafiny_Object $product, int $qty = 1, ?int $saleId = null): Leafiny_Object
    {
        if ($saleId === null) {
            $saleId = $this->getCurrentId(true);
        }
        if ($saleId === null) {
            return new Leafiny_Object();
        }

        /** @var Commerce_Model_Sale_Item $model */
        $model = App::getObject('model', 'sale_item');

        $item = $model->getItem($saleId, $product->getData('product_id'), 'product_id');

        if ($item->getData('item_id')) {
            $qty = (int)$item->getData('qty') + $qty;
        }

        $item->setData('original_qty', (int)$item->getData('qty') ?: 0);
        $item->setData('qty', $qty);
        $item->setData('requested_qty', $qty);

        $item->setData('sale_id', $saleId);

        if ($item->getData('qty') > $product->getData('qty')) {
            $item->setData('qty', (int)$product->getData('qty'));
        }

        App::dispatchEvent('cart_add_product_before', ['item' => $item, 'product' => $product]);

        $this->updateItem($item, $product);
        $this->calculation($saleId);

        App::dispatchEvent('cart_add_product_after', ['item' => $item, 'product' => $product]);

        return $item;
    }

    /**
     * Update item with product data
     *
     * @param Leafiny_Object $item
     * @param Leafiny_Object $product
     *
     * @return int|null
     */
    public function updateItem(Leafiny_Object $item, Leafiny_Object $product): ?int
    {
        if (!$item->getData('qty')) {
            return null;
        }

        /** @var Commerce_Model_Sale_Item $model */
        $model = App::getObject('model', 'sale_item');

        $priceInclTax = $product->getData('prices_incl_tax')->getData('final_price');
        $priceExclTax = $product->getData('prices_excl_tax')->getData('final_price');

        if ($item->getData('custom_incl_tax_unit') !== null) {
            $priceInclTax = $item->getData('custom_incl_tax_unit');
        }
        if ($item->getData('custom_excl_tax_unit') !== null) {
            $priceExclTax = $item->getData('custom_excl_tax_unit');
        }

        $item->addData(
            [
                'product_id'    => $product->getData('product_id'),
                'product_sku'   => $product->getData('sku'),
                'product_name'  => $product->getData('name'),
                'product_path'  => $product->getData('path_key'),
                'sale_id'       => $item->getData('sale_id'),
                'qty'           => $item->getData('qty'),
                'incl_tax_unit' => $priceInclTax,
                'excl_tax_unit' => $priceExclTax,
                'weight_unit'   => $product->getData('weight'),
                'tax_rule_id'   => $product->getData('tax_rule_id'),
                'tax_percent'   => $product->getData('tax_percent'),
                'max_qty'       => $product->getData('qty')
            ]
        );

        App::dispatchEvent('sale_update_item', ['item' => $item, 'product' => $product]);

        try {
            return $model->save($item);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }

    /**
     * Refresh sale items
     *
     * @var int|null $saleId
     *
     * @return void
     */
    public function refreshItems(?int $saleId = null): void
    {
        try {
            if ($saleId === null) {
                $saleId = $this->getCurrentId(true);
            }
            if ($saleId === null) {
                return;
            }

            $items = $this->getItems($saleId);

            foreach ($items as $item) {
                $product = $item->getData('product');
                if ($product->getData('product_id')) {
                    $this->updateItem($item, $product);
                }
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }

    /**
     * Remove Item
     *
     * @param int      $itemId
     * @param int|null $saleId
     *
     * @return bool
     */
    public function removeItem(int $itemId, ?int $saleId = null): bool
    {
        /** @var Commerce_Model_Sale_Item $model */
        $model = App::getObject('model', 'sale_item');

        try {
            if ($saleId === null) {
                $saleId = $this->getCurrentId(true);
            }
            if ($saleId === null) {
                return false;
            }

            $item = $this->getItem($itemId, $saleId);
            if (!$item) {
                return false;
            }

            if ($item->getData('sale_id') !== $saleId) {
                return false;
            }

            App::dispatchEvent('cart_remove_item_before', ['item' => $item, 'sale_id' => $saleId]);

            if ($model->delete($itemId)) {
                $this->calculation($saleId);

                App::dispatchEvent('cart_remove_item_after', ['item' => $item, 'sale_id' => $saleId]);

                return true;
            }

            return false;
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return false;
    }

    /**
     * Retrieve item for cart
     *
     * @param int      $itemId
     * @param int|null $saleId
     *
     * @return Leafiny_Object|null
     */
    public function getItem(int $itemId, ?int $saleId = null): ?Leafiny_Object
    {
        /** @var Commerce_Model_Sale_Item $model */
        $model = App::getObject('model', 'sale_item');

        try {
            if ($saleId === null) {
                $saleId = $this->getCurrentId(true);
            }
            if ($saleId === null) {
                return null;
            }

            $item = $model->getItem($saleId, $itemId);
            if (!$item->getData('item_id')) {
                return null;
            }

            return $item;
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }

    /**
     * Retrieve items for sale id
     *
     * @param int|null $saleId
     * @param bool     $addProduct
     *
     * @return Leafiny_Object[]
     */
    public function getItems(?int $saleId = null, bool $addProduct = true): array
    {
        /** @var Commerce_Model_Sale_Item $model */
        $model = App::getSingleton('model', 'sale_item');

        try {
            if ($saleId === null) {
                $saleId = $this->getCurrentId();
            }
            if ($saleId === null) {
                return [];
            }

            $items = $model->getItems($saleId);

            if ($addProduct) {
                /** @var Catalog_Model_Product $productModel */
                $productModel = App::getSingleton('model', 'catalog_product');

                foreach ($items as $item) {
                    if (!$item->getData('product_id')) {
                        continue;
                    }
                    $item->setData('product', $productModel->get((int)$item->getData('product_id')));
                }
            }

            return $items;
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }

    /**
     * Calculate sale totals
     *
     * @param int|null $saleId
     *
     * @return bool
     * @throws Exception
     */
    public function calculation(?int $saleId = null): bool
    {
        if ($this->collectTotals($saleId)) {
            /** @var Commerce_Helper_Cart_Rule $cartRuleHelper */
            $cartRuleHelper = App::getSingleton('helper', 'cart_rule');
            $cartRuleHelper->applyNoCouponCartRules($saleId);
            $cartRuleHelper->refreshFreeGift($saleId);
            $cartRuleHelper->refreshItemsDiscount($saleId);

            App::dispatchEvent('collect_totals_before', ['sale_id' => $saleId]);

            return $this->collectTotals($saleId);
        }

        return false;
    }

    /**
     * Collect Totals
     *
     * @param int|null $saleId
     * @return bool
     * @throws Exception
     */
    protected function collectTotals(?int $saleId): bool
    {
        if ($saleId === null) {
            $saleId = $this->getCurrentId(true);
        }
        if ($saleId === null) {
            return false;
        }

        $sale = $this->getSale($saleId);

        if (!$sale->getData('sale_id')) {
            return false;
        }

        /* Refresh */
        $this->refreshItems($saleId);

        /* Init */
        $inclTaxTotal = 0;
        $exclTaxTotal = 0;
        $taxTotal = 0;

        $items = $this->getItems($saleId, false);

        /* Subtotal with discount */
        $inclTaxSubtotal = 0;
        $exclTaxSubtotal = 0;
        $taxSubtotal = 0;
        $totalWeight = 0;
        $totalQty = 0;
        $inclTaxDiscount = 0;
        $exclTaxDiscount = 0;
        $taxDiscount = 0;

        foreach ($items as $item) {
            $inclTaxSubtotal += $item->getData('incl_tax_row');
            $exclTaxSubtotal += $item->getData('excl_tax_row');
            $inclTaxDiscount += $item->getData('discount_incl_tax_row');
            $exclTaxDiscount += $item->getData('discount_excl_tax_row');
            $taxSubtotal += $item->getData('tax_amount_row');
            $taxDiscount += $item->getData('discount_tax_row');
            $totalWeight += $item->getData('weight_row');
            $totalQty += $item->getData('qty');
        }

        $sale->setData('incl_tax_subtotal', $inclTaxSubtotal);
        $sale->setData('excl_tax_subtotal', $exclTaxSubtotal);
        $sale->setData('incl_tax_discount', $inclTaxDiscount);
        $sale->setData('excl_tax_discount', $exclTaxDiscount);
        $sale->setData('incl_tax_subtotal_with_discount', $inclTaxSubtotal - $inclTaxDiscount);
        $sale->setData('excl_tax_subtotal_with_discount', $exclTaxSubtotal - $exclTaxDiscount);
        $sale->setData('tax_subtotal', $taxSubtotal);
        $sale->setData('tax_discount', $taxDiscount);
        $sale->setData('total_weight', $totalWeight);
        $sale->setData('total_qty', $totalQty);

        $inclTaxTotal += $inclTaxSubtotal - $inclTaxDiscount;
        $exclTaxTotal += $exclTaxSubtotal - $exclTaxDiscount;
        $taxTotal += $taxSubtotal - $taxDiscount;

        /* Shipping */
        $inclTaxShipping = 0;
        $exclTaxShipping = 0;
        $taxShipping = 0;

        if ($sale->getData('shipping_method')) {
            /** @var Commerce_Helper_Shipping $shippingHelper */
            $shippingHelper = App::getSingleton('helper', 'shipping');

            $method = $shippingHelper->getMethod(
                $sale->getData('shipping_method'),
                $totalWeight,
                $this->getAddress('shipping', $sale)
            );

            /** @var Commerce_Helper_Cart_Rule $cartRuleHelper */
            $cartRuleHelper = App::getSingleton('helper', 'cart_rule');
            $shippingDiscountRate = $cartRuleHelper->getShippingDiscountRate($sale);

            $inclTaxShipping = $method->getData('prices_incl_tax')->getData('final_price') * $shippingDiscountRate;
            $exclTaxShipping = $method->getData('prices_excl_tax')->getData('final_price') * $shippingDiscountRate;
            $taxShipping = ($inclTaxShipping - $exclTaxShipping) * $shippingDiscountRate;
        }

        $sale->setData('incl_tax_shipping', $inclTaxShipping);
        $sale->setData('excl_tax_shipping', $exclTaxShipping);
        $sale->setData('tax_shipping', $taxShipping);

        $inclTaxTotal += $inclTaxShipping;
        $exclTaxTotal += $exclTaxShipping;
        $taxTotal += $taxShipping;

        $sale->setData('incl_tax_total', $inclTaxTotal);
        $sale->setData('excl_tax_total', $exclTaxTotal);
        $sale->setData('tax_total', $taxTotal);

        $sale->setData('sale_currency', $this->getCurrency());

        return (bool)$this->getSaleObject()->save($sale);
    }

    /**
     * Retrieve sale
     *
     * @param int $saleId
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getSale(int $saleId): Leafiny_Object
    {
        $sale = $this->getSaleObject()->get($saleId);

        if ($sale->getData('state') !== Commerce_Model_Sale::SALE_STATE_CART) {
            return new Leafiny_Object();
        }

        return $sale;
    }

    /**
     * Retrieve current sale
     *
     * @return Leafiny_Object|null
     * @throws Exception
     */
    public function getCurrentSale(): ?Leafiny_Object
    {
        $saleId = $this->getCurrentId();

        if (!$saleId) {
            return null;
        }

        return $this->getSale($saleId);
    }

    /**
     * Retrieve address
     *
     * @param string              $type
     * @param Leafiny_Object|null $sale
     *
     * @return Leafiny_Object|null
     */
    public function getAddress(string $type, ?Leafiny_Object $sale = null): ?Leafiny_Object
    {
        try {
            /** @var Commerce_Model_Sale_Address $model */
            $model = App::getSingleton('model', 'sale_address');

            if ($sale === null) {
                $sale = $this->getCurrentSale();
            }

            return $model->getBySaleId((int)$sale->getData('sale_id'), $type);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }

    /**
     * Validate address
     *
     * @param Leafiny_Object $address
     *
     * @return string[]
     */
    public function validateAddress(Leafiny_Object $address): array
    {
        $errors = [];

        if (!$address->getData('firstname')) {
            $errors[] = 'Firstname is required';
        }
        if (!$address->getData('lastname')) {
            $errors[] = 'Lastname is required';
        }
        if (!$address->getData('street_1')) {
            $errors[] = 'Address is required';
        }
        if (!$address->getData('city')) {
            $errors[] = 'City is required';
        }
        if (!$address->getData('postcode')) {
            $errors[] = 'Postcode is required';
        }
        if (!$address->getData('country_code')) {
            $errors[] = 'Country is required';
        }

        return $errors;
    }

    /**
     * Init empty sale
     *
     * @return int|null
     * @throws Exception
     */
    public function initSale(): ?int
    {
        $saleId = $this->getSaleObject()->save(new Leafiny_Object());

        App::dispatchEvent('sale_init_after', ['sale_id' => $saleId]);

        return $saleId;
    }

    /**
     * Current cart exist test
     *
     * @param int $saleId
     *
     * @return bool
     * @throws Exception
     */
    public function isCartExist(int $saleId): bool
    {
        if ($this->getData('is_cart_exist_flag') !== null) {
            return $this->getData('is_cart_exist_flag');
        }

        $isCartExist = (bool)$this->getSaleObject()->size(
            [
                [
                    'column' => 'sale_id',
                    'value'  => $saleId
                ],
                [
                    'column' => 'state',
                    'value'  => Commerce_Model_Sale::SALE_STATE_CART
                ],
            ]
        );

        $this->setData('is_cart_exist_flag', $isCartExist);

        return $isCartExist;
    }

    /**
     * Format currency
     *
     * @param mixed       $value
     * @param string|null $currency
     *
     * @return string
     */
    public function formatCurrency($value, ?string $currency = null): string
    {
        $value = (float)str_replace(',', '.', $value);

        $formatter = new NumberFormatter(App::getLanguage(), NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($value, $currency ?: $this->getCurrency()) ?: '';
    }

    /**
     * Retrieve Sale Object
     *
     * @return Commerce_Model_Sale
     */
    public function getSaleObject(): Commerce_Model_Sale
    {
        return App::getObject('model', 'sale');
    }
}
