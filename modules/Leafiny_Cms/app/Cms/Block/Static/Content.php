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
        $block = $model->getByKey($this->getObjectKey(), App::getLanguage());

        if (!$block->getData('status')) {
            return null;
        }

        return $block->getData('content');
    }
}
