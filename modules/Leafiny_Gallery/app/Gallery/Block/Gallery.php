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
 * Class Gallery_Block_Gallery
 */
class Gallery_Block_Gallery extends Core_Block
{
    /**
     * Current group
     *
     * @var Leafiny_Object $group
     */
    protected $group = null;

    /**
     * Retrieve group
     *
     * @return Leafiny_Object
     */
    public function getGroup(): Leafiny_Object
    {
        if ($this->group !== null) {
            return $this->group;
        }

        /** @var Gallery_Model_Group $groupModel */
        $groupModel = App::getSingleton('model', 'gallery_group');

        $this->group = $groupModel->getByKey($this->getObjectKey(), App::getLanguage());

        return $this->group;
    }

    /**
     * Retrieve group type
     *
     * @return string|null
     */
    public function getGroupName(): ?string
    {
        return $this->getGroup()->getData('name');
    }

    /**
     * Retrieve group type
     *
     * @return string|null
     */
    public function getGroupType(): ?string
    {
        return $this->getGroup()->getData('type');
    }

    /**
     * Group is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        $group = $this->getGroup();

        if (!$group->getData('group_id')) {
            return false;
        }

        if (!$group->getData('status')) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve gallery images
     *
     * @return array
     * @throws Exception
     */
    public function getImages(): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        /** @var Gallery_Model_Image $gallery */
        $gallery = App::getObject('model', 'gallery_image');

        return $gallery->getActivatedImages((int)$this->getGroup()->getData('group_id'), 'gallery_group');
    }
}
