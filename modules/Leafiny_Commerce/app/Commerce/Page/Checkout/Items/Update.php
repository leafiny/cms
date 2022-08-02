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
 * Class Commerce_Page_Checkout_Items_Update
 */
class Commerce_Page_Checkout_Items_Update extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $params = $this->getPost();

        $quantities = $params->getData('qty');

        if (!$quantities) {
            $this->redirect($this->getRefererUrl());
        }

        /** @var Core_Helper_Crypt $encrypt */
        $encrypt = App::getObject('helper_crypt');
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');
        /** @var Commerce_Model_Sale_Item $model */
        $model = App::getObject('model', 'sale_item');

        try {
            if (!$helper->getCurrentId()) {
                $this->redirect($this->getRefererUrl());
            }

            foreach ($quantities->getData() as $idCrypt => $qty) {
                $itemId = $encrypt->decrypt($idCrypt);

                $item = $helper->getItem((int)$itemId);
                if (!$item) {
                    continue;
                }

                $product = $this->getProduct((int)$item->getData('product_id'));
                if (!$product) {
                    continue;
                }

                $qty = abs((int)$qty) ?: 1;

                if ($qty > (int)$product->getData('qty')) {
                    $qty = (int)$product->getData('qty');
                    $this->setWarningMessage(App::translate('The requested quantity is not available'));
                }

                $item->setData('qty', $qty);
                $model->save($item);
            }

            $helper->calculation($helper->getCurrentId());
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->setErrorMessage(App::translate('Cannot update quantities right now'));
        }

        /** @var Commerce_Helper_Checkout $helperCheckout */
        $helperCheckout = App::getSingleton('helper', 'checkout');
        $this->redirect($this->getUrl($helperCheckout->getStepUrl('cart')));
    }

    /**
     * Retrieve product
     *
     * @param int $productId
     *
     * @return Leafiny_Object|null
     */
    protected function getProduct(int $productId): ?Leafiny_Object
    {
        /** @var Catalog_Model_Product $model */
        $model = App::getObject('model', 'catalog_product');

        try {
            return $model->get($productId);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }
}
