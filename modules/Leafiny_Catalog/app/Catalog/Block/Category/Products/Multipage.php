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
 * Class Catalog_Block_Category_Products_Multipage
 */
class Catalog_Block_Category_Products_Multipage extends Core_Block
{
    /**
     * @var int|null $totalPages
     */
    protected $totalPages = null;

    /**
     * Retrieve current page number
     *
     * @return int
     */
    public function getPageNumber(): int
    {
        return (int)$this->getParentObjectParams()->getData(Catalog_Helper_Catalog_Product::URL_PARAM_PAGE) ?: 1;
    }

    /**
     * Retrieve total pages number
     *
     * @return int
     * @throws Exception
     */
    public function getTotalPages(): int
    {
        if ($this->totalPages !== null) {
            return $this->totalPages;
        }

        $categoryId = $this->getCustom('category_id');

        if (!$categoryId) {
            return 0;
        }

        /** @var Catalog_Helper_Catalog_Product $helper */
        $helper = App::getSingleton('helper', 'catalog_product');

        $this->totalPages = (int)ceil($helper->getTotalCategoryProducts($categoryId) / $helper->getLimit()) ?: 1;

        return $this->totalPages;
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
        $url = App::getUrlRewrite($page->getObjectKey(), 'category');

        $params = $page->getParams(['get']);
        $params->setData(Catalog_Helper_Catalog_Product::URL_PARAM_PAGE);
        if ($number > 1) {
            $params->setData(Catalog_Helper_Catalog_Product::URL_PARAM_PAGE, $number);
        }

        $query = [];
        foreach ($params->getData() as $param => $value) {
            $query[$param] = $value instanceof Leafiny_Object ? $value->getData() : $value;
        }
        $url .= '?' . http_build_query($query);

        return $url;
    }
}
