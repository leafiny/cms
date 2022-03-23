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
 * Class Commerce_Page_Backend_Order_History_Post
 */
class Commerce_Page_Backend_Order_History_Post extends Backend_Page_Admin_Page_Abstract
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

        $saleId     = $post->getData('sale_id');
        $statusCode = $post->getData('status_code');
        $language   = $post->getData('language');

        if (!$saleId) {
            $this->setErrorMessage(App::translate('Sale identifier is required'));
            $this->redirect($this->getRefererUrl());
        }
        if (!$statusCode) {
            $this->setErrorMessage(App::translate('Status is required'));
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
            $status = $statusModel->getByCode($statusCode, $language);

            if (!$status->getData('status_id')) {
                throw new Exception(App::translate('This status does not exist'));
            }

            $sale->setData('status', $status->getData('code'));
            $orderModel->save($sale);

            if (empty($post->getData('comment'))) {
                $post->setData('comment', $status->getData('comment'));
            }

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

            $this->setSuccessMessage(App::translate('Comment has been added'));

        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
        }

        $this->redirect($this->getRefererUrl());
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
