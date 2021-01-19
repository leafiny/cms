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
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        $params = $this->getParams();

        if (!$params->getData('image_id')) {
            $this->redirect($this->getRefererUrl(), true);
        }

        $imageId = $params->getData('image_id');

        /** @var Gallery_Model_Image $model */
        $model = App::getObject('model', 'gallery_image');

        $image = $model->get($imageId);

        if (!$image->getData('image_id')) {
            $this->redirect($this->getRefererUrl(), true);
        }

        $model->delete($image->getData('image_id'));

        /** @var Backend_Helper_Data $helper */
        $helper = App::getObject('helper', 'admin_data');

        $file = $helper->getMediaDir() . $image->getData('image');

        if (is_file($file)) {
            unlink($file);
        }

        $this->setSuccessMessage($this->translate('Image has been deleted'));

        $this->redirect($this->getRefererUrl(), true);
    }
}
