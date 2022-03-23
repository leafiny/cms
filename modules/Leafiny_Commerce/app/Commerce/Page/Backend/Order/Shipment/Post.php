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

            /** @var Commerce_Model_Sale_Status $statusModel */
            $statusModel = App::getSingleton('model', 'sale_status');
            $status = $statusModel->get(Commerce_Model_Sale_Status::SALE_STATUS_SHIPPED, 'code');

            if (!$status->getData('status_id')) {
                throw new Exception(App::translate('This status does not exist'));
            }

            $sale->setData('status', $status->getData('code'));
            $orderModel->save($sale);

            $comment = (string)$status->getData('comment');

            $tracking = !empty($post->getData('tracking')) ? $post->getData('tracking') : '';

            if (!empty($tracking)) {
                $comment .= $this->getTackingNumberComment($tracking);
            }

            $post->setData('status_code', $status->getData('code'));
            $post->setData('comment', $comment);
            $post->setData('operator', $this->getOperator());

            /** @var Commerce_Model_Sale_History $historyModel */
            $historyModel = App::getSingleton('model', 'sale_history');
            $historyId = $historyModel->save($post);

            App::dispatchEvent(
                'backend_object_save_after',
                [
                    'data'       => $post,
                    'identifier' => 'sale_history',
                    'object_id'  => $historyId
                ]
            );

            App::dispatchEvent(
                'backend_add_tracking_number',
                [
                    'comment'  => $post,
                    'sale'     => $sale,
                    'tracking' => $tracking,
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
     * @param string $trackingNumber
     *
     * @return string
     */
    public function getTackingNumberComment(string $trackingNumber): string
    {
        return "\n\n" . App::translate('Tracking number:') . ' ' . $trackingNumber;
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
