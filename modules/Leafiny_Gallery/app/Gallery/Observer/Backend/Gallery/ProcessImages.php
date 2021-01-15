<?php

declare(strict_types=1);

/**
 * Class Gallery_Observer_Backend_Gallery_ProcessImages
 */
class Gallery_Observer_Backend_Gallery_ProcessImages extends Core_Event implements Core_Interface_Event
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $data */
        $data = $object->getData('data');

        /** @var int $objectId */
        $objectId = $object->getData('object_id');
        if (!$objectId) {
            return;
        }

        /** @var Gallery_Model_Image $model */
        $model = App::getObject('model', 'gallery_image');

        if ($data->getData('gallery_image')) {
            $image = $data->getData('gallery_image');
            if (isset($image['position'])) {
                foreach ($image['position'] as $imageId => $position) {
                    $data = new Leafiny_Object();
                    $data->setData(
                        [
                            'image_id' => $imageId,
                            'position' => $position,
                        ]
                    );
                    $model->save($data);
                }
            }
        }

        if (!isset($_FILES['gallery_images'])) {
            return;
        }

        /** @var Backend_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'admin_data');
        /** @var Core_Helper_File $helperFile */
        $helperFile = App::getObject('helper_file');

        $images = $_FILES['gallery_images'];

        foreach ($images['name'] as $key => $name) {
            if (empty($name)) {
                continue;
            }

            $file = pathinfo($name);

            $filename  = $helper->formatKey($name, [$file['extension']]) . '-' . uniqid() . '.' . $file['extension'];
            $directory = $helper->getMediaDir() . Gallery_Model_Image::GALLERY_IMAGE_MEDIA_DIR . DS;

            $image = [
                'name'     => $images['name'][$key],
                'tmp_name' => $images['tmp_name'][$key],
                'size'     => $images['size'][$key],
                'error'    => $images['error'][$key],
            ];

            if (!$helperFile->uploadFile($image, $directory . $filename, $this->getAllowedExtensions())) {
                continue;
            }

            /** @var string $identifier */
            $identifier = $object->getData('identifier');

            $size = $model->size(
                [
                    [
                        'column'   => 'entity_id',
                        'value'    => $objectId,
                        'operator' => '='
                    ],
                    [
                        'column'   => 'entity_type',
                        'value'    => $identifier,
                        'operator' => '='
                    ],
                ]
            );

            $data = new Leafiny_Object();
            $data->setData(
                [
                    'image'       => Gallery_Model_Image::GALLERY_IMAGE_MEDIA_DIR . '/' . $filename,
                    'entity_id'   => $objectId,
                    'entity_type' => $identifier,
                    'position'    => $size + 1,
                ]
            );
            $model->save($data);
        }
    }

    /**
     * Retrieve allowed extensions
     *
     * @return string[]
     */
    public function getAllowedExtensions(): array
    {
        if (is_array($this->getCustom('allowed_extensions'))) {
            return $this->getCustom('allowed_extensions');
        }

        return [];
    }
}
