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
 * Class Commerce_Model_Sale_History
 */
class Commerce_Model_Sale_History extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_sale_history';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'history_id';

    /**
     * Save object
     *
     * @param Leafiny_Object $object
     *
     * @return int|null
     * @throws Exception|Throwable
     */
    public function save(Leafiny_Object $object): ?int
    {
        if (!$object->getData('history_id') && $object->getData('status_code')) {
            /** @var Commerce_Model_Sale_Status $model */
            $model = App::getSingleton('model', 'sale_status');
            $status = $model->getByCode($object->getData('status_code'), $object->getData('language'));

            if ($status) {
                $object->setData('comment', $object->getData('comment') ?: $status->getData('comment'));
                $object->setData('title', $object->getData('title') ?: $status->getData('label'));
            }
        }

        $historyId = parent::save($object);

        $this->sendMail($object);

        return $historyId;
    }

    /**
     * Send history to customer by mail
     * @TODO: use observer to send the mail with history_save_after event
     *
     * @param Leafiny_Object $object
     *
     * @return bool
     * @throws Throwable
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendMail(Leafiny_Object $object): bool
    {
        if (!$object->getData('send_mail')) {
            return false;
        }

        /** @var Core_Mail $mail */
        $mail = App::getSingleton('mail', 'history');

        /** @var Commerce_Model_Sale $saleModel */
        $saleModel = App::getSingleton('model', 'sale');
        $sale = $saleModel->get($object->getData('sale_id'));

        $mail->setRecipientEmail($sale->getData('email'));

        $result = $mail->send(['history' => $object, 'sale' => $sale]);

        if ($result) {
            $object->setData('send_mail', false);
            $object->setData('mail_sent', 1);
            $this->save($object);
        }

        return $result;
    }

    /**
     * Retrieve history by sale id
     *
     * @param int $saleId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getBySaleId(int $saleId): array
    {
        $filters = [
            [
                'column' => 'sale_id',
                'value'  => $saleId,
            ]
        ];
        $orders = [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ],
            [
                'order' => 'history_id',
                'dir'   => 'DESC',
            ]
        ];

        return parent::getList($filters, $orders);
    }

    /**
     * Shipping validation
     *
     * @param Leafiny_Object $object
     *
     * @return string
     */
    public function validate(Leafiny_Object $object): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$object->getData('sale_id')) {
            return 'The sale identifier cannot be empty';
        }

        return '';
    }
}
