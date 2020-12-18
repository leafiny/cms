<?php

declare(strict_types=1);

/**
 * Class Editor_Block_Backend_Form_Editor
 */
class Editor_Block_Backend_Form_Editor extends Core_Block
{
    /**
     * Retrieve select name
     *
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->getCustom('name');
    }

    /**
     * Retrieve Label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return (string)$this->getCustom('label');
    }

    /**
     * Retrieve actions
     *
     * @return string[]
     */
    public function getActions(): array
    {
        $actions = $this->getCustom('actions');

        if (empty($actions)) {
            return ['Markdown', 'HTML', 'Preview'];
        }

        $actions = array_filter($actions);

        return $actions;
    }
}
