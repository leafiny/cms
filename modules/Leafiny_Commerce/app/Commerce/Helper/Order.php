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
 * Class Commerce_Helper_Order
 */
class Commerce_Helper_Order extends Core_Helper
{
    /**
     * @var string[]
     */
    protected $statuses = null;

    /**
     * Complete the order after payment
     *
     * @param Leafiny_Object $sale
     *
     * @return void
     * @throws Exception|Throwable
     */
    public function complete(Leafiny_Object $sale): void
    {
        /** @var Commerce_Model_Sale_Item $itemModel */
        $itemModel = App::getSingleton('model', 'sale_item');
        $items = $itemModel->getItems($sale->getData('sale_id'));

        App::dispatchEvent('order_complete_before', ['sale' => $sale, 'items' => $items]);

        /** @var Commerce_Model_Sale $saleModel */
        $saleModel = App::getSingleton('model', 'sale');

        /* Update state */
        $sale->setData('state', Commerce_Model_Sale::SALE_STATE_ORDER);
        $saleModel->save($sale);

        /* Invoice */
        if (!$sale->getData('no_invoice')) {
            $saleModel->invoice($sale->getData('sale_id'));
        }

        /* Add event to history */
        if (!$sale->getData('no_history')) {
            /** @var Commerce_Model_Sale_History $historyModel */
            $historyModel = App::getSingleton('model', 'sale_history');
            $historyModel->save(
                new Leafiny_Object(
                    [
                        'sale_id' => $sale->getData('sale_id'),
                        'status_code' => $sale->getData('status'),
                        'language' => App::getLanguage()
                    ]
                )
            );
        }

        /* Decrease product stock */
        if (!$sale->getData('no_stock')) {
            /** @var Catalog_Model_Product $productModel */
            $productModel = App::getSingleton('model', 'catalog_product');

            foreach ($items as $item) {
                $product = $productModel->get($item->getData('product_id'));
                $product->setData('qty', $product->getData('qty') - $item->getData('qty'));
                $productModel->save($product);
            }
        }

        /* Send mail to customer */
        if (!$sale->getData('no_email')) {
            /** @var Commerce_Mail_Order $mail */
            $mail = App::getSingleton('mail', 'order');
            $mail->setRecipientEmail($sale->getData('email'));
            $mail->send(['sale' => $sale]);
        }

        App::dispatchEvent('order_complete_after', ['sale' => $sale, 'items' => $items]);
    }

    /**
     * Retrieve order link
     *
     * @param string $key
     *
     * @return string
     */
    public function getOrderLink(string $key): string
    {
        return App::getBaseUrl() . 'order/progress/' . $key . '/';
    }

    /**
     * Retrieve default increment id
     *
     * @param string $customConfig
     *
     * @return string
     */
    public function getDefaultIncrementId(string $customConfig): string
    {
        return (string)$this->getCustom($customConfig);
    }

    /**
     * Retrieve all status
     *
     * @param string $language
     *
     * @return string[]
     */
    public function getStatuses(string $language = 'en_US'): array
    {
        if ($this->statuses !== null) {
            return $this->statuses;
        }

        $this->statuses = [];

        try {
            /** @var Commerce_Model_Sale_Status $statusModel */
            $statusModel = App::getSingleton('model', 'sale_status');

            $statuses = $statusModel->getStatuses($language);

            foreach ($statuses as $status) {
                $this->statuses[$status->getData('code')] = $status->getData('label');
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $this->statuses;
    }
}
