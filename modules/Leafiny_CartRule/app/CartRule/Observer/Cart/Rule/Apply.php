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
 * Class CartRule_Observer_Cart_Rule_Apply
 */
class CartRule_Observer_Cart_Rule_Apply extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Gift specific data
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        $saleId = $object->getData('sale_id');
        if (!$saleId) {
            return;
        }

        /** @var CartRule_Helper_Cart_Rule $cartRuleHelper */
        $cartRuleHelper = App::getSingleton('helper', 'cart_rule');

        $cartRuleHelper->applyNoCouponCartRules($saleId);
        $cartRuleHelper->addFreeGift($saleId);
        $cartRuleHelper->refreshItemsDiscount($saleId);
    }
}
