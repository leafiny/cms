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
 * Class Gallery_Page_Backend_Gallery_Image_Delete
 */
class Gallery_Page_Backend_Gallery_Image_Delete extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $params = $this->getParams();

        if (!$params->getData('image_id')) {
            $this->redirect($this->getRefererUrl());
        }

        $imageId = $params->getData('image_id');

        /** @var Gallery_Model_Image $model */
        $model = App::getObject('model', 'gallery_image');

        try {
            $image = $model->get($imageId);

            if (!$image->getData('image_id')) {
                $this->redirect($this->getRefererUrl());
            }

            if ($model->delete($image->getData('image_id'))) {
                /** @var Core_Helper_File $helper */
                $helper = App::getObject('helper_file');
                $file = $helper->getMediaDir() . $image->getData('image');
                $helper->unlink($file);

                $this->setSuccessMessage($this->translate('Image has been deleted'));
            }
        } catch (Throwable $throwable) {
            $this->setErrorMessage(App::translate($throwable->getMessage()));
        }

        $this->redirect($this->getRefererUrl());
    }
}
