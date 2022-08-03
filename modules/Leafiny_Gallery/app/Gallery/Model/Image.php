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
    public const GALLERY_IMAGE_MEDIA_DIR = 'gallery';

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
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return null;
        }

        $adapter->where('entity_id', $entityId);
        $adapter->where('entity_type', $entityType);
        $adapter->where('position', 1);
        $adapter->where('status', 1);

        $images = $adapter->get($this->getMainTable(), [0, 1]);

        if ($adapter->getLastErrno()) {
            throw new Exception($adapter->getLastError());
        }

        if (empty($images)) {
            return null;
        }

        return new Leafiny_Object(reset($images));
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
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('entity_id', $entityId);
        $adapter->where('entity_type', $entityType);
        $adapter->where('status', 1);
        $adapter->orderBy('position', 'ASC');

        $images = $adapter->get($this->getMainTable());

        if ($adapter->getLastErrno()) {
            throw new Exception($adapter->getLastError());
        }

        foreach ($images as $key => $data) {
            $object = new Leafiny_Object();
            $object->setData($data);

            $images[$key] = $object;
        }

        return $images;
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

        if ($adapter->getLastErrno()) {
            throw new Exception($adapter->getLastError());
        }

        return true;
    }
}
