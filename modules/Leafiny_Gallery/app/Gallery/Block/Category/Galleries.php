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
 * Class Gallery_Block_Category_Galleries
 */
class Gallery_Block_Category_Galleries extends Core_Block
{
    /**
     * Retrieve groups
     *
     * @param int $categoryId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getGroups(int $categoryId): array
    {
        if ($this->getCustom('groups')) {
            return $this->getCustom('groups');
        }

        /** @var Gallery_Helper_Gallery_Group $helper */
        $helper = App::getSingleton('helper', 'gallery_group');

        return $helper->getCategoryGroups($categoryId);
    }

    /**
     * Retrieve gallery images
     *
     * @param int $groupId
     *
     * @return array
     * @throws Exception
     */
    public function getImages(int $groupId): array
    {
        /** @var Gallery_Model_Image $gallery */
        $gallery = App::getObject('model', 'gallery_image');

        return $gallery->getActivatedImages($groupId, 'gallery_group');
    }
}
