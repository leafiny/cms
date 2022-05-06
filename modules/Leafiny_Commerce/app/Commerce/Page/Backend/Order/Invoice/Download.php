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
 * Class Commerce_Page_Backend_Order_Invoice_Download
 */
class Commerce_Page_Backend_Order_Invoice_Download extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $params = $this->getParams();

        $saleId = (int)$params->getData('sale_id');

        if (!$saleId) {
            $this->redirect($this->getRefererUrl());
        }

        try {
            /** @var Commerce_Model_Sale $orderModel */
            $orderModel = App::getSingleton('model', 'sale');
            $sale = $orderModel->get($saleId);

            if (!$sale->getData('sale_id')) {
                throw new Exception(App::translate('This order does not exist'));
            }

            /** @var Commerce_Model_Sale_Address $addressModel */
            $addressModel = App::getSingleton('model', 'sale_address');
            $address = $addressModel->getBySaleId($saleId, 'billing');

            /** @var Commerce_Model_Sale_Item $model */
            $itemModel = App::getObject('model', 'sale_item');
            $items = $itemModel->getItems($saleId);

            $sale->setData('buyer', $address);
            $sale->setData('items', $items);

            /** @var Commerce_Helper_Invoice $helper */
            $helper = App::getSingleton('helper', 'invoice');

            $sale->setData('logo', $helper->getCustom('logo'));
            $sale->setData('fonts', $helper->getCustom('fonts'));
            $sale->setData('info', $helper->getCustom('info'));
            $sale->setData('date', strftime($helper->getCustom('date'), strtotime($sale->getData('created_at'))));
            $sale->setData('seller', new Leafiny_Object($helper->getCustom('seller')));
            $sale->setData('legal', $helper->getCustom('legal'));

            $helper->getPdf($sale);
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
            $this->redirect($this->getRefererUrl());
        }
    }
}
