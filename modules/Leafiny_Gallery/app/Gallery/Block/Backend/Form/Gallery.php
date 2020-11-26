<?php

declare(strict_types=1);

/**
 * Class Gallery_Block_Backend_Form_Gallery
 */
class Gallery_Block_Backend_Form_Gallery extends Core_Block
{
    /**
     * Retrieve select name
     *
     * @return string
     */
    public function getInputFileName(): string
    {
        return (string)$this->getCustom('input_file_name');
    }

    /**
     * Retrieve select name
     *
     * @return string
     */
    public function getInputDataName(): string
    {
        return (string)$this->getCustom('input_data_name');
    }

    /**
     * Retrieve max file size
     *
     * @return int
     */
    public function getMaxFileSize(): int
    {
        /** @var Core_Helper_File $fileHelper */
        $fileHelper = App::getObject('helper_file');

        return $fileHelper->getMaxFileSize();
    }

    /**
     * Retrieve image delete URL
     *
     * @param int $imageId
     *
     * @return string
     */
    public function getDeleteUrl(int $imageId): string
    {
        $identifier = $this->getCustom('delete_identifier');

        if (!$identifier) {
            return '#';
        }

        return $this->getUrl($identifier) . '?image_id=' . $imageId;
    }

    /**
     * Retrieve image status URL
     *
     * @param int $imageId
     *
     * @return string
     */
    public function getStatusUrl(int $imageId): string
    {
        $identifier = $this->getCustom('status_identifier');

        if (!$identifier) {
            return '#';
        }

        return $this->getUrl($identifier) . '?image_id=' . $imageId;
    }

    /**
     * Retrieve gallery images
     *
     * @param int    $entityId
     * @param string $entityType
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getImages(int $entityId, string $entityType): array
    {
        /** @var Gallery_Model_Image $model */
        $model = App::getObject('model', 'gallery_image');

        $filters = [
            [
                'column'   => 'entity_id',
                'value'    => $entityId,
                'operator' => '='
            ],
            [
                'column'   => 'entity_type',
                'value'    => $entityType,
                'operator' => '='
            ],
        ];

        $order = [
            'order' => 'position',
            'dir'   => 'ASC',
        ];

        return $model->getList($filters, [$order]);
    }
}
