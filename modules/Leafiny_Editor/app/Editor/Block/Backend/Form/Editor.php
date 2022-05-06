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

        return array_filter($actions);
    }
}
