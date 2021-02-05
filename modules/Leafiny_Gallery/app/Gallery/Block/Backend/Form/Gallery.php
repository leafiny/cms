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
     * Retrieve container size
     *
     * @return int
     */
    public function getContainerSize(): int
    {
        $numberPerRow = $this->getCustom('number_per_row');

        return $numberPerRow ? 24 / (int)$numberPerRow : 4;
    }

    /**
     * Retrieve image tag
     *
     * @param Leafiny_Object $image
     *
     * @return string
     */
    public function getTag(Leafiny_Object $image): string
    {
        $tag = '&lt;img
            src=&quot;&lbrace;&lbrace; page.getMediaUrl &rbrace;&rbrace;' . $image->getData('image') . '&quot;
            alt=&quot;' . $image->getData('label') . '&quot;
        /&gt;';

        $link = $image->getData('link');

        if ($link) {
            if (!preg_match('/^http/', strtolower($link))) {
                $link = '&lbrace;&lbrace; page.getUrl(\'' . $link . '\') &rbrace;&rbrace;';
            }
            $tag = '&lt;a href=&quot;' . $link . '&quot;&gt;' . $tag . '&lt;/a&gt;';
        }

        return preg_replace('/\s+/', ' ', $tag);
    }

    /**
     * Retrieve if label must be shown
     *
     * @return bool
     */
    public function showLabel(): bool
    {
        return (bool)$this->getCustom('show_label');
    }

    /**
     * Retrieve if tag must be shown
     *
     * @return bool
     */
    public function showTag(): bool
    {
        return (bool)$this->getCustom('show_tag');
    }

    /**
     * Retrieve if link must be shown
     *
     * @return bool
     */
    public function showLink(): bool
    {
        return (bool)$this->getCustom('show_link');
    }

    /**
     * Retrieve if position must be shown
     *
     * @return bool
     */
    public function showPosition(): bool
    {
        return (bool)$this->getCustom('show_position');
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
