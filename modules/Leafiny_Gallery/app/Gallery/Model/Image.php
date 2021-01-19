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
 * Class Gallery_Model_Image
 */
class Gallery_Model_Image extends Core_Model
{
    /**
     * Gallery image directory in media
     *
     * @var string GALLERY_IMAGE_MEDIA_DIR
     */
    const GALLERY_IMAGE_MEDIA_DIR = 'gallery';

    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'gallery_image';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'image_id';

    /**
     * Retrieve main image data
     *
     * @param int    $entityId
     * @param string $entityType
     *
     * @return Leafiny_Object|null
     * @throws Exception
     */
    public function getMainImage(int $entityId, string $entityType): ?Leafiny_Object
    {
        $filters = [
            [
                'column'   => 'entity_id',
                'value'    => $entityId,
                'operator' => '=',
            ],
            [
                'column'   => 'entity_type',
                'value'    => $entityType,
                'operator' => '='
            ],
            [
                'column'   => 'position',
                'value'    => 1,
                'operator' => '=',
            ],
            [
                'column'   => 'status',
                'value'    => 1,
                'operator' => '=',
            ],
        ];

        $images = $this->getList($filters, [], [0, 1]);

        if (empty($images)) {
            return null;
        }

        return reset($images);
    }

    /**
     * Retrieve active images
     *
     * @param int    $entityId
     * @param string $entityType
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getActivatedImages(int $entityId, string $entityType): array
    {
        $filters = [
            [
                'column'   => 'entity_id',
                'value'    => $entityId,
                'operator' => '=',
            ],
            [
                'column'   => 'entity_type',
                'value'    => $entityType,
                'operator' => '='
            ],
            [
                'column'   => 'status',
                'value'    => 1,
                'operator' => '=',
            ],
        ];

        $orders = [
            [
                'order' => 'position',
                'dir'   => 'ASC',
            ]
        ];

        return $this->getList($filters, $orders);
    }

    /**
     * Delete Entity Images
     *
     * @param int $entityId
     * @param string $entityType
     *
     * @return bool
     * @throws Exception
     */
    public function deleteEntityImages(int $entityId, string $entityType): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('entity_id', $entityId);
        $adapter->where('entity_type', $entityType);
        $adapter->delete($this->getMainTable());

        return true;
    }
}
