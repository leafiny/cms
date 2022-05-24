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
 * Class Catalog_Observer_Page
 */
class Catalog_Observer_Page extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Execute
     *
     * @param Core_Page|Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Core_Page $page */
        $page = $object->getData('object');

        if ($page->getObjectIdentifier() !== '/category/*.html') {
            return;
        }

        /** @var Category_Model_Category $category */
        $category = $page->getCustom('category');

        if (!$category) {
            return;
        }

        /** @var string $pageNumber */
        $pageNumber = $page->getObjectParams()->getData(Catalog_Helper_Catalog_Product::URL_PARAM_PAGE) ?: 1;

        /** @var Catalog_Helper_Catalog_Product $helper */
        $helper = App::getSingleton('helper', 'catalog_product');

        $products = $helper->getCategoryProducts((int)$category->getData('category_id'), (int)$pageNumber);

        if ($pageNumber > 1 && empty($products)) {
            $page->error(true);
            return;
        }

        if ($pageNumber > 1) {
            if ($page->getCustom('canonical')) {
                $page->setCustom(
                    'canonical',
                    $page->getCustom('canonical') . '?' . Catalog_Helper_Catalog_Product::URL_PARAM_PAGE . '=' . $pageNumber
                );
            }
            $page->setCustom(
                'meta_title',
                $page->getCustom('meta_title') . ' - ' . App::translate('Page') . ' ' . $pageNumber
            );
        }

        /** @var Catalog_Block_Category_Products $block */
        $block = App::getSingleton('block', 'category.products');
        $block->setCustom('products', $products);
    }
}
