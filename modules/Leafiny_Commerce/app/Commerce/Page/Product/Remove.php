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
 * Class Commerce_Page_Product_Remove
 */
class Commerce_Page_Product_Remove extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        /** @var Core_Helper_Crypt $encrypt */
        $encrypt = App::getObject('helper_crypt');

        $itemId = $encrypt->decrypt((string)$this->getObjectKey());

        if ($itemId) {
            /** @var Commerce_Helper_Cart $helper */
            $helper = App::getSingleton('helper', 'cart');
            if ($helper->removeItem((int)$itemId)) {
                $this->setSuccessMessage(
                    $this->translate('The product has been removed from the cart')
                );
                App::dispatchEvent(
                    'cart_remove_product_action_after',
                    ['page' => $this, 'item_id' => (int)$itemId]
                );
            }
        }

        $this->redirect($this->getRefererUrl());
    }
}
