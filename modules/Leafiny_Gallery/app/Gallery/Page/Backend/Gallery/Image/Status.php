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
 * Class Gallery_Page_Backend_Gallery_Image_Status
 */
class Gallery_Page_Backend_Gallery_Image_Status extends Backend_Page_Admin_Page_Abstract
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
            $this->redirect($this->getRefererUrl());
        }

        $imageId = $params->getData('image_id');

        /** @var Gallery_Model_Image $model */
        $model = App::getObject('model', 'gallery_image');

        $image = $model->get($imageId);

        if (!$image->getData('image_id')) {
            $this->redirect($this->getRefererUrl());
        }

        $image->setData('status', !$image->getData('status'));
        $model->save($image);

        $this->setSuccessMessage($this->translate('Image status has been updated'));

        $this->redirect($this->getRefererUrl());
    }
}
