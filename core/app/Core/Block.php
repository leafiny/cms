<?php

declare(strict_types=1);

/**
 * Class Core_Block
 */
class Core_Block extends Core_Template_Abstract
{
    /**
     * Retrieve template file
     *
     * @return string|null
     * @throws Exception
     */
    public function getTemplate(): ?string
    {
        if ($this->getContext() !== $this->getCurrentContext()) {
            throw new Exception(
                'Block "' . $this->getObjectIdentifier() . '" is not allowed
                    in "' . $this->getCurrentContext() . '" context'
            );
        }

        return parent::getTemplate();
    }

    /**
     * Retrieve current context
     *
     * @return string
     */
    public function getCurrentContext(): string
    {
        return $this->getData('current_context');
    }

    /**
     * Set Current Context
     *
     * @param string $context
     *
     * @return void
     */
    public function setCurrentContext(string $context): void
    {
        $this->setData('current_context', $context);
    }
}
