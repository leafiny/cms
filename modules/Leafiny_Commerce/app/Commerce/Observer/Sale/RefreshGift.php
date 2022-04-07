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
 * Class Commerce_Observer_Sale_RefreshGift
 */
class Commerce_Observer_Sale_RefreshGift extends Core_Observer implements Core_Interface_Observer
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
        /** @var Leafiny_Object $item */
        $item = $object->getData('item');

        if ($this->isFreeGift($item)) {
            $item->setData('product_path', null);
            $item->setData('can_update', 0);
            $item->setData('incl_tax_unit', 0);
            $item->setData('excl_tax_unit', 0);
            $item->setData('qty', 1);
        }
    }

    /**
     * Item is a gift
     *
     * @param Leafiny_Object $item
     *
     * @return bool
     */
    protected function isFreeGift(Leafiny_Object $item): bool
    {
        /** @var Commerce_Helper_Cart_Rule $helperCartRule */
        $helperCartRule = App::getObject('helper', 'cart_rule');

        return (bool)$helperCartRule->getFreeGiftRuleId($item);
    }
}
