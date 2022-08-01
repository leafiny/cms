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

class Frontend_Block_Widget_Gallery_Banner extends Core_Block
{
    /**
     * Retrieve images from the group
     *
     * @return Leafiny_Object[]
     */
    Public function getImages(): array
    {
        /** @var Gallery_Model_Group $groupModel */
        $groupModel = App::getSingleton('model', 'gallery_group');

        try {
            $group = $groupModel->get($this->getCustom('group_key'), 'path_key');
            if ($group->hasData()) {
                /** @var Gallery_Model_Image $gallery */
                $gallery = App::getSingleton('model', 'gallery_image');

                return $gallery->getActivatedImages((int)$group->getData('group_id'), 'gallery_group');
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }
}
