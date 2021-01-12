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

        return $this->secureChildBlocks($block->getData('content'));
    }

    /**
     * Secure child blocks
     *
     * @param string|null $content
     *
     * @return string|null
     */
    protected function secureChildBlocks(?string $content): ?string
    {
        if (!$content) {
            return $content;
        }

        $identifier = $this->getIdentifierPath($this->getObjectIdentifier());
        $pattern = '/{{ (\'|")' . $identifier . '(\'|")\|block }}/';

        if (preg_match($pattern, $content)) {
            /** @var Log_Model_File $log */
            $log = App::getObject('model', 'log_file');
            $log->add(
                'Unable to load child block "' . $identifier . '" (infinite loop)',
                Log_Model_Log_Interface::ERR
            );

            return preg_replace($pattern, '', $content);
        }

        return $content;
    }
}
