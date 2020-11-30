<?php

declare(strict_types=1);

/**
 * Class Gallery_Observer_Backend_Gallery_DeleteImages
 */
class Gallery_Observer_Backend_Gallery_DeleteImages extends Core_Event
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
        /** @var string $objectIdentifier */
        $objectIdentifier = $object->getData('identifier');
        /** @var string $objectIdentifier */
        $objectId = $object->getData('object_id');

        if ($objectIdentifier && $objectId) {
            /** @var Gallery_Model_Image $gallery */
            $gallery = App::getObject('model', 'gallery_image');

            $gallery->deleteEntityImages($objectId, $objectIdentifier);
        }
    }
}
