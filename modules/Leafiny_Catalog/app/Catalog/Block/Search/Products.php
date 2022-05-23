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
 * Class Catalog_Block_Search_Products
 */
class Catalog_Block_Search_Products extends Core_Block
{
    /**
     * Retrieve products
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getProducts(): array
    {
        if (!$this->getProductIds()) {
            return [];
        }

        /** @var Catalog_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'catalog_product');
        $helper->setCustom(
            'filters',
            [
                'product_id' => [
                    'column'   => 'product_id',
                    'value'    => $this->getProductIds(),
                    'operator' => 'IN',
                ],
            ]
        );

        $orders = [
            [
                'order'  => 'product_id',
                'dir'    => 'ASC',
                'custom' => $this->getProductIds(),
            ]
        ];

        /** @var Catalog_Model_Product $model */
        $model = App::getObject('model', 'catalog_product');

        return $model->getList($helper->getFilters(), $orders, $this->getLimit());
    }

    /**
     * Retrieve limit
     *
     * @return int[]
     */
    public function getLimit(): array
    {
        return $this->getCustom('limit') ?? [0, 100];
    }

    /**
     * Retrieve object Ids
     *
     * @return array
     */
    public function getProductIds(): array
    {
        if (!$this->getResult()) {
            return [];
        }

        return $this->getResult()->getData('response') ?? [];
    }

    /**
     * Retrieve search result
     *
     * @return Leafiny_Object|null
     */
    public function getResult(): ?Leafiny_Object
    {
        return $this->getCustom('result');
    }
}
