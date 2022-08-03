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
 * Class Catalog_Page_Product_View
 */
class Catalog_Page_Product_View extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        if (!$this->getObjectKey()) {
            $this->error(true);
            return;
        }

        $product = $this->getProduct($this->getObjectKey());

        if (!$product) {
            $this->error(true);
            return;
        }

        if (!$product->getData('status')) {
            $this->error(true);
            return;
        }

        $this->setCustom('product', $product);
        $this->setCustom('breadcrumb', $this->getBreadcrumb($product));

        $this->getHelper()->dynamicMetadata($this->getCustom('product_dynamic_metadata') ?? [], $product);

        $this->setCustom('canonical', $product->getData('canonical'));
        $this->setCustom('meta_title', $product->getData('meta_title'));
        $this->setCustom('meta_description', $product->getData('meta_description'));
        if ($product->getData('robots')) {
            $this->setCustom('robots', $product->getData('robots'));
        }
    }

    /**
     * Retrieve product
     *
     * @param string $pathKey
     *
     * @return Leafiny_Object|null
     */
    protected function getProduct(string $pathKey): ?Leafiny_Object
    {
        /** @var Catalog_Model_Product $model */
        $model = App::getObject('model', 'catalog_product');
        $product = $model->getByKey($pathKey, App::getLanguage());

        if (!$product->getData('product_id')) {
            return null;
        }

        return $product;
    }

    /**
     * Retrieve breadcrumb
     *
     * @param Leafiny_Object $product
     *
     * @return string[]
     */
    public function getBreadcrumb(Leafiny_Object $product): array
    {
        $links = [];

        if ($product->getData('breadcrumb')) {
            /** @var Category_Helper_Category $helper */
            $helper = App::getObject('helper', 'category');

            if ($helper instanceof Category_Helper_Category) {
                $links = $helper->getBreadcrumb($product->getData('breadcrumb'));
            }
        }

        foreach ($links as $name => $url) {
            $product->setData('_category_' . ($i = ($i ?? 0) + 1), $name);
        }

        $links[$product->getData('name')] = null;

        return $links;
    }

    /**
     * Retrieve product main image
     *
     * @param int $productId
     *
     * @return Leafiny_Object|null
     */
    public function getMainImage(int $productId): ?Leafiny_Object
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
     * Retrieve all product images
     *
     * @param int $productId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getImages(int $productId): array
    {
        /** @var Gallery_Model_Image $gallery */
        $gallery = App::getObject('model', 'gallery_image');

        return $gallery->getActivatedImages($productId, 'catalog_product');
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
