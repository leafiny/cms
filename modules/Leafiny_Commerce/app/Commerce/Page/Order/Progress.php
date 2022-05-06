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
 * Class Commerce_Page_Order_Progress
 */
class Commerce_Page_Order_Progress extends Core_Page
{
    /**
     * @var Leafiny_Object $sale
     */
    protected $sale = null;

    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        $key = $this->getObjectKey();

        if (!$key) {
            $this->redirect();
        }

        try {
            /** @var Commerce_Model_Sale $model */
            $model = App::getSingleton('model', 'sale');
            $sale = $model->get($key, 'key');
            if ($sale->getData('state') !== Commerce_Model_Sale::SALE_STATE_ORDER) {
                $this->redirect();
            }
            $this->sale = $sale;
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->redirect();
        }
    }

    /**
     * Retrieve order history
     *
     * @return Leafiny_Object[]
     */
    public function getHistory(): array
    {
        /** @var Commerce_Model_Sale_History $historyModel */
        $historyModel = App::getSingleton('model', 'sale_history');

        try {
            return $historyModel->getBySaleId($this->getSale()->getData('sale_id'));
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }

    /**
     * Retrieve sale
     *
     * @return Leafiny_Object|null
     */
    public function getSale(): ?Leafiny_Object
    {
        return $this->sale;
    }

    /**
     * Retrieve all status
     *
     * @return string[]
     */
    public function getStatuses(): array
    {
        /** @var Commerce_Helper_Order $helperOrder */
        $helperOrder = App::getSingleton('helper', 'order');

        return $helperOrder->getStatuses($this->getSale()->getData('language'));
    }

    /**
     * Retrieve status label by code
     *
     * @param string $code
     *
     * @return string
     */
    public function getStatus(string $code): string
    {
        $statuses = $this->getStatuses();

        return isset($statuses[$code]) ? $statuses[$code] : $code;
    }

    /**
     * Retrieve date in locale
     *
     * @param string $date
     *
     * @return string
     */
    public function formatDate(string $date): string
    {
        return strftime('%d %b %Y %H:%M:%S', strtotime($date));
    }
}
