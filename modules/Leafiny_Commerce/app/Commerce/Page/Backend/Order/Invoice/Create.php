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
 * Class Commerce_Page_Backend_Order_Invoice_Create
 */
class Commerce_Page_Backend_Order_Invoice_Create extends Backend_Page_Admin_Page_Abstract
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

            $orderModel->invoice((int)$sale->getData('sale_id'));

            $this->setSuccessMessage(
                App::translate('The invoice has been created')
            );
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
        }

        $this->redirect($this->getRefererUrl());
    }
}
