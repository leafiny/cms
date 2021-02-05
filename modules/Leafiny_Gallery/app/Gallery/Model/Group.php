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
 * Class Gallery_Model_Group
 */
class Gallery_Model_Group extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'gallery_group';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'group_id';

    /**
     * Save
     *
     * @param Leafiny_Object $object
     *
     * @return int|null
     * @throws Exception
     */
    public function save(Leafiny_Object $object): ?int
    {
        if ($object->getData('path_key')) {
            /** @var Core_Helper $helper */
            $helper = App::getObject('helper');
            $object->setData('path_key', $helper->formatKey($object->getData('path_key')));
        }

        return parent::save($object);
    }

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        /** @var Gallery_Model_Image $model */
        $model = App::getObject('model', 'gallery_image');
        /** @var Core_Helper_File $helper */
        $helper = App::getObject('helper_file');

        $filters = [
            [
                'column'   => 'entity_id',
                'value'    => $id,
            ],
            [
                'column'   => 'entity_type',
                'value'    => $this->getObjectIdentifier(),
            ],
        ];

        $images = $model->getList($filters);
        foreach ($images as $image) {
            $helper->unlink($helper->getMediaDir() . $image->getData('image'));
        }

        $model->deleteEntityImages($id, $this->getObjectIdentifier());

        return parent::delete($id);
    }

    /**
     * Retrieve group by key and language
     *
     * @param string      $key
     * @param string|null $language
     *
     * @return Leafiny_Object
     */
    public function getByKey(string $key, ?string $language = null): Leafiny_Object
    {
        $object = new Leafiny_Object();

        try {
            $adapter = $this->getAdapter();
            if (!$adapter) {
                return $object;
            }

            if ($language) {
                $adapter->where('language', $language);
            }

            return parent::get($key, 'path_key');
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $object;
    }

    /**
     * Product validation
     *
     * @param Leafiny_Object $product
     *
     * @return string
     */
    public function validate(Leafiny_Object $product): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$product->getData('name')) {
            return 'The name cannot be empty';
        }
        if (!$product->getData('path_key')) {
            return 'The key cannot be empty';
        }

        return '';
    }
}
