<?php

declare(strict_types=1);

/**
 * Class Catalog_Block_Category_Product
 */
class Catalog_Block_Category_Product extends Core_Block
{
    /**
     * Retrieve products
     *
     * @param int $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getProducts(int $categoryId): array
    {
        /** @var Catalog_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'catalog_product');

        return $helper->getCategoryProducts($categoryId, $this->getPageNumber());
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
        } catch (Exception $exception) {
            /** @var Log_Model_File $log */
            $log = App::getObject('model', 'log_file');
            $log->add($exception->getMessage());
        }

        return null;
    }

    /**
     * Retrieve current page number
     *
     * @return int
     */
    public function getPageNumber(): int
    {
        return (int)$this->getParentObjectParams()->getData(Catalog_Helper_Data::URL_PARAM_PAGE) ?: 1;
    }

    /**
     * Retrieve total pages number
     *
     * @param int $categoryId
     *
     * @return int
     * @throws Exception
     */
    public function getTotalPages(int $categoryId): int
    {
        /** @var Catalog_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'catalog_product');

        return (int)ceil($helper->getTotalCategoryProducts($categoryId) / $helper->getLimit()) ?: 1;
    }

    /**
     * Retrieve page URL
     *
     * @param Core_Page $page
     * @param int       $number
     *
     * @return string
     */
    public function getPageUrl(Core_Page $page, int $number): string
    {
        $url = $page->getUrl($page->getObjectIdentifier());

        if ($number > 1) {
            $url .= '?' . Catalog_Helper_Data::URL_PARAM_PAGE . '=' . $number;
        }

        return $url;
    }
}
