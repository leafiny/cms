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

class Frontend_Block_Widget_Product_New extends Core_Block
{
    /**
     * Retrieve new products
     *
     * @return Leafiny_Object[]
     */
    public function getProducts(): array
    {
        $filters = [
            'status' => [
                'column' => 'status',
                'value'  => 1,
            ],
            'language' => [
                'column' => 'language',
                'value'  => App::getLanguage(),
            ],
        ];

        $sortOrders = [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ],
        ];

        $limit = [0, $this->getCustom('number') ?? 6];

        /** @var Catalog_Model_Product $model */
        $model = App::getSingleton('model', 'catalog_product');

        try {
            return $model->getList($filters, $sortOrders, $limit);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }
}
