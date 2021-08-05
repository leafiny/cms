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
 * Class Gallery_Block_Category_Gallery
 */
class Gallery_Block_Category_Gallery extends Core_Block
{
    /**
     * Retrieve groups
     *
     * @param int $categoryId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getGroups(int $categoryId): array
    {
        /** @var Gallery_Model_Group $groupModel */
        $groupModel = App::getSingleton('model', 'gallery_group');

        try {
            return $groupModel
                ->addCategoryFilter($categoryId)
                ->getList(
                    $this->getFilters(),
                    $this->getOrders()
                );
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            return [];
        }
    }

    /**
     * Retrieve gallery images
     *
     * @param int $groupId
     *
     * @return array
     * @throws Exception
     */
    public function getImages(int $groupId): array
    {
        /** @var Gallery_Model_Image $gallery */
        $gallery = App::getObject('model', 'gallery_image');

        return $gallery->getActivatedImages($groupId, 'gallery_group');
    }

    /**
     * Retrieve gallery group filters
     *
     * @return array[]
     */
    public function getFilters(): array
    {
        return [
            [
                'column'   => 'status',
                'value'    => 1,
            ],
            [
                'column' => 'language',
                'value'  => App::getLanguage(),
            ]
        ];
    }

    /**
     * Retrieve sort orders
     *
     * @return array
     */
    public function getOrders(): array
    {
        return [];
    }
}
