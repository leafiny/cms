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
 * Class Commerce_Page_Backend_Order_View
 */
class Commerce_Page_Backend_Order_View extends Backend_Page_Admin_Page_Abstract
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
        $params = $this->getParams();

        if (!$params->getData('id')) {
            $this->redirect($this->getRefererUrl());
        }

        try {
            $sale = $this->getModel()->get($params->getData('id'));
            if ($sale->getData('state') !== Commerce_Model_Sale::SALE_STATE_ORDER) {
                $this->setErrorMessage($this->translate('This element no longer exists'));
                $this->redirect($this->getRefererUrl());
            }
            $this->sale = $sale;
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
            $this->redirect($this->getRefererUrl());
        }
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
     * Retrieve order link
     *
     * @return string
     */
    public function getOrderLink(): string
    {
        /** @var Commerce_Helper_Order $orderHelper */
        $orderHelper = App::getSingleton('helper', 'order');

        return $orderHelper->getOrderLink($this->sale->getData('key'));
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
        /** @var Commerce_Model_Sale_Address $model */
        $model = App::getSingleton('model', 'sale_address');

        try {
            return $model->getBySaleId($this->getSale()->getData('sale_id'), $type);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }

    /**
     * Retrieve order items
     *
     * @return Leafiny_Object[]
     */
    public function getItems(): array
    {
        /** @var Commerce_Model_Sale_Item $model */
        $model = App::getObject('model', 'sale_item');

        try {
            return $model->getItems($this->getSale()->getData('sale_id'));
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
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
     * Render options if needed
     *
     * @param string|null $options
     *
     * @return void
     * @throws Exception
     */
    public function renderItemOption(?string $options): void
    {
        if ($options) {
            /** @var array $result */
            $result = json_decode($options, true);

            if (!empty($result['render_block'])) {
                $this->blockHtml('admin.' . $result['render_block'], $result);
            }
        }
    }

    /**
     * Retrieve post URL
     *
     * @return string
     */
    public function getHistoryPostUrl(): string
    {
        return $this->getUrl('/admin/*/order/history/post/');
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
     * @param string|null $code
     *
     * @return string|null
     */
    public function getStatus(?string $code): ?string
    {
        $statuses = $this->getStatuses();

        return isset($statuses[$code]) ? $statuses[$code] : $code;
    }

    /**
     * Retrieve post URL
     *
     * @return string
     */
    public function getShipmentPostUrl(): string
    {
        return $this->getUrl('/admin/*/order/shipment/post/');
    }

    /**
     * Retrieve download invoice URL
     *
     * @param int $saleId
     *
     * @return string
     */
    public function getDownloadInvoiceUrl(int $saleId): string
    {
        return $this->getUrl('/admin/*/order/invoice/download/') . '?sale_id=' . $saleId;
    }

    /**
     * Retrieve invoice URL
     *
     * @param int $saleId
     *
     * @return string
     */
    public function getCreateInvoiceUrl(int $saleId): string
    {
        return $this->getUrl('/admin/*/order/invoice/create/') . '?sale_id=' . $saleId;
    }

    /**
     * Sale can be shipped
     *
     * @param Leafiny_Object $sale
     *
     * @return bool
     */
    public function canShip(Leafiny_Object $sale): bool
    {
        return $sale->getData('status') !== Commerce_Model_Sale_Status::SALE_STATUS_SHIPPED;
    }

    /**
     * Retrieve country
     *
     * @param string $countryCode
     *
     * @return string
     */
    public function getCountry(string $countryCode): string
    {
        $countries = $this->getAllCountries();

        return isset($countries[$countryCode]) ? $countries[$countryCode] : $countryCode;
    }

    /**
     * Retrieve model
     *
     * @return Core_Model
     */
    public function getModel(): Core_Model
    {
        return App::getSingleton('model', $this->getModelIdentifier());
    }

    /**
     * Retrieve Model Identifier
     *
     * @return string|null
     */
    public function getModelIdentifier(): ?string
    {
        $modelIdentifier = $this->getCustom('model_identifier');

        return $modelIdentifier ? (string)$modelIdentifier : null;
    }

    /**
     * Retrieve product main image
     *
     * @param int $productId
     *
     * @return Leafiny_Object|null
     */
    public function getProductImage(int $productId): ?Leafiny_Object
    {
        /** @var Gallery_Model_Image $image */
        $image = App::getObject('model', 'gallery_image');

        try {
            return $image->getMainImage($productId, 'catalog_product');
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }

    /**
     * Retrieve Children blocks
     *
     * @return string[]
     */
    public function getChildren(): array
    {
        $children = $this->getCustom('children');

        if (!$children) {
            return [];
        }

        $children = array_filter($children, 'strlen');

        asort($children);

        return array_keys($children);
    }
}
