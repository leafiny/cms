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
 * Class Commerce_Page_Product_Add
 */
class Commerce_Page_Product_Add extends Core_Page
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

        $productId = $encrypt->decrypt((string)$this->getObjectKey());

        if (!$productId) {
            $this->redirect($this->getRefererUrl());
        }

        /** @var Catalog_Model_Product $model */
        $model = App::getObject('model', 'catalog_product');

        $qty = $this->getPost()->getData('qty') ?: 1;

        try {
            $product = $model->get((int)$productId);

            App::dispatchEvent('cart_add_product_action_before', ['page' => $this, 'product' => $product]);
            if ($product->getData('product_id')) {
                /** @var Commerce_Helper_Cart $helper */
                $helper = App::getSingleton('helper', 'cart');
                $item = $helper->addProduct($product, (int)$qty);

                if ($item->getData('requested_qty') > $item->getData('qty')) {
                    $this->setWarningMessage(
                        $this->translate('You have reached the maximum quantity for this product')
                    );
                }

                if ($item->getData('qty') > $item->getData('original_qty')) {
                    $this->setSuccessMessage(
                        $this->translate('The product has been added to the cart')
                    );
                }
                App::dispatchEvent(
                    'cart_add_product_action_after',
                    ['page' => $this, 'product' => $product, 'item' => $item]
                );
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->setErrorMessage(App::translate('Cannot add this product right now'));
        }

        $this->redirect($this->getRefererUrl());
    }
}
