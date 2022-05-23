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
 * Class Cms_Block_Static_Content
 */
class Cms_Block_Static_Content extends Core_Block
{
    /**
     * Retrieve Block Content
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        /** @var Cms_Model_Block $model */
        $model = App::getObject('model', 'cms_block');
        /** @var Core_Block $block */
        $block = $model->getByKey($this->getObjectKey(), App::getLanguage());

        if (!$block->getData('status')) {
            return null;
        }

        /** @var Cms_Helper_Cms_Block $helper */
        $helper = App::getObject('helper', 'cms_block');
        $helper->secureChildBlocks($block);

        return $block->getData('content');
    }
}
