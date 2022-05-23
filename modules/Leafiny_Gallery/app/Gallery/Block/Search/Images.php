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
 * Class Gallery_Block_Search_Images
 */
class Gallery_Block_Search_Images extends Core_Block
{
    /**
     * Retrieve images
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getImages(): array
    {
        if (!$this->getImageIds()) {
            return [];
        }

        $orders = [
            [
                'order'  => 'image_id',
                'dir'    => 'ASC',
                'custom' => $this->getImageIds(),
            ]
        ];

        /** @var Gallery_Model_Image $model */
        $model = App::getObject('model', 'gallery_image');

        return $model->getList($this->getFilters(), $orders, $this->getLimit());
    }

    /**
     * Retrieve limit
     *
     * @return int[]
     */
    public function getLimit(): array
    {
        return $this->getCustom('limit') ?: [0, 100];
    }

    /**
     * Retrieve gallery group filters
     *
     * @return array[]
     */
    public function getFilters(): array
    {
        $filters = [
            [
                'column' => 'status',
                'value'  => 1,
            ],
            [
                'column'   => 'image_id',
                'value'    => $this->getImageIds(),
                'operator' => 'IN',
            ],
        ];

        return array_merge($filters, $this->getCustom('image_filters') ?: []);
    }

    /**
     * Retrieve object Ids
     *
     * @return array
     */
    public function getImageIds(): array
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
