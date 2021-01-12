<?php

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

        /** @var Cms_Helper_Cms $helper */
        $helper = App::getObject('helper', 'cms');
        $helper->secureChildBlocks($block);

        return $block->getData('content');
    }
}
