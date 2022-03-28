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
 * Class Commerce_Page_Backend_Order_Shipment_Post
 */
class Commerce_Page_Backend_Order_Shipment_Post extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $post = $this->getPost();

        $saleId   = $post->getData('sale_id');
        $language = $post->getData('language');

        if (!$saleId) {
            $this->setErrorMessage(App::translate('Sale identifier is required'));
            $this->redirect($this->getRefererUrl());
        }
        if (!$language) {
            $this->setErrorMessage(App::translate('Language is required'));
            $this->redirect($this->getRefererUrl());
        }

        try {
            /** @var Commerce_Model_Sale $orderModel */
            $orderModel = App::getSingleton('model', 'sale');
            $sale = $orderModel->get($saleId);

            if (!$sale->getData('sale_id')) {
                throw new Exception(App::translate('This order does not exist'));
            }

            $trackingNumber = !empty($post->getData('tracking')) ? $post->getData('tracking') : '';

            $shipment = $this->addSaleShipment($sale, $trackingNumber);

            $this->updateSaleStatus($sale, $shipment);

            App::dispatchEvent(
                'backend_add_tracking_number',
                [
                    'comment'  => $post,
                    'sale'     => $sale,
                    'tracking' => $trackingNumber,
                ]
            );

            $this->setSuccessMessage(App::translate('The sale has been shipped'));
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
        }

        $this->redirect($this->getRefererUrl());
    }

    /**
     * Retrieve tracking number comment
     *
     * @param Leafiny_Object $shipment
     *
     * @return string|null
     */
    protected function getHistoryMessageComment(Leafiny_Object $shipment): ?string
    {
        if (!$shipment->getData('tracking_number')) {
            return null;
        }

        $comment = "\n\n" . App::translate('Tracking number:') . ' ' . $shipment->getData('tracking_number');

        if ($shipment->getData('tracking_url')) {
            $comment .= "\n\n" . App::translate('Tracking URL:') . ' ' . $shipment->getData('tracking_url');
        }

        return $comment;
    }

    /**
     * Update sale status and add message in history
     *
     * @param Leafiny_Object      $sale
     * @param Leafiny_Object|null $shipment
     *
     * @return int|null
     * @throws Throwable
     */
    protected function updateSaleStatus(Leafiny_Object $sale, ?Leafiny_Object $shipment): ?int
    {
        $post = $this->getPost();

        /** @var Commerce_Model_Sale_Status $statusModel */
        $statusModel = App::getSingleton('model', 'sale_status');
        /** @var Commerce_Model_Sale $orderModel */
        $orderModel = App::getSingleton('model', 'sale');

        $status = $statusModel->get(Commerce_Model_Sale_Status::SALE_STATUS_SHIPPED, 'code');

        if (!$status->getData('status_id')) {
            throw new Exception(App::translate('This status does not exist'));
        }

        $sale->setData('status', $status->getData('code'));
        $orderModel->save($sale);

        $comment = (string)$status->getData('comment');
        if ($shipment) {
            $comment .= $this->getHistoryMessageComment($shipment);
        }

        $history = new Leafiny_Object(
            [
                'sale_id'     => $sale->getData('sale_id'),
                'status_code' => $status->getData('code'),
                'comment'     => $comment,
                'operator'    => $this->getOperator(),
                'language'    => $post->getData('language'),
                'send_mail'   => $post->getData('send_mail') ?: 0,
            ]
        );

        /** @var Commerce_Model_Sale_History $historyModel */
        $historyModel = App::getSingleton('model', 'sale_history');
        $historyId = $historyModel->save($history);

        App::dispatchEvent(
            'backend_object_save_after',
            [
                'data'       => $history,
                'identifier' => $historyModel->getObjectIdentifier(),
                'object_id'  => $historyId
            ]
        );

        return $historyId;
    }

    /**
     * Add sale shipment
     *
     * @param Leafiny_Object $sale
     * @param string|null    $trackingNumber
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    protected function addSaleShipment(Leafiny_Object $sale, ?string $trackingNumber): Leafiny_Object
    {
        /** @var Commerce_Model_Sale_Shipment $shipmentModel */
        $shipmentModel = App::getObject('model', 'sale_shipment');
        /** @var Commerce_Model_Shipping $shippingModel */
        $shippingModel = App::getObject('model', 'shipping');

        $method = $shippingModel->get($sale->getData('shipping_method'), 'method');

        $shipmentId = $shipmentModel->save(
            new Leafiny_Object(
                [
                    'sale_id'         => $sale->getData('sale_id'),
                    'tracking_number' => $trackingNumber,
                    'tracking_url'    => $trackingNumber ? $method->getData('tracking_url') : null,
                    'shipping_method' => $method->getData('method'),
                ]
            )
        );

        return $shipmentModel->get($shipmentId);
    }

    /**
     * Retrieve user full name
     *
     * @return string
     * @throws Exception
     */
    protected function getOperator(): string
    {
        /** @var Backend_Model_Admin_User $userModel */
        $userModel = App::getSingleton('model', 'admin_user');
        $user = $userModel->getCurrentUser();

        return $user->getData('firstname') . ' ' . $user->getData('lastname');
    }
}
