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
 * Class Commerce_Mail_Order
 */
class Commerce_Mail_Order extends Core_Mail
{
    /**
     * Render options if needed
     *
     * @param string|null $options
     *
     * @return void
     * @throws Exception
     */
    public function getItemOptions(?string $options): void
    {
        if ($options) {
            /** @var array $result */
            $result = json_decode($options, true);

            if (!empty($result['render_block'])) {
                $this->blockHtml((string)$result['render_block'], $result);
            }
        }
    }

    /**
     * Retrieve all items
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getItems(): array
    {
        if ($this->getData('items')) {
            return $this->getData('items');
        }

        /** @var Commerce_Model_Sale_Item $itemModel */
        $itemModel = App::getObject('model', 'sale_item');

        return $itemModel->getItems($this->getSale()->getData('sale_id'));
    }

    /**
     * Retrieve address
     *
     * @param string $type
     *
     * @return Leafiny_Object|null
     */
    public function getAddress(string $type): ?Leafiny_Object
    {
        try {
            /** @var Commerce_Model_Sale_Address $model */
            $model = App::getSingleton('model', 'sale_address');

            return $model->getBySaleId($this->getSale()->getData('sale_id'), $type);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }

    /**
     * Retrieve payment method
     *
     * @return Commerce_Interface_Payment|null
     */
    public function getPaymentMethod(): ?Commerce_Interface_Payment
    {
        $method = $this->getSale()->getData('payment_method');

        if ($method) {
            /** @var Commerce_Interface_Payment $payment */
            return App::getObject('model', $method);
        }

        return null;
    }

    /**
     * Retrieve shipping method
     *
     * @return Leafiny_Object|null
     */
    public function getShippingMethod(): ?Leafiny_Object
    {
        $method = $this->getSale()->getData('shipping_method');

        try {
            /** @var Commerce_Model_Shipping $shippingModel */
            $shippingModel = App::getObject('model', 'shipping');
            $method = $shippingModel->get($method, 'method');
            if ($method->getData('shipping_id')) {
                return $method;
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }

    /**
     * Retrieve sale
     *
     * @return Leafiny_Object
     */
    public function getSale(): Leafiny_Object
    {
        return $this->getData('sale');
    }
}
