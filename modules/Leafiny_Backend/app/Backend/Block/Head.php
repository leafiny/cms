<?php

declare(strict_types=1);

/**
 * Class Backend_Block_Head
 */
class Backend_Block_Head extends Core_Block
{
    /**
     * Retrieve Stylesheets
     *
     * @return string[]
     */
    public function getStylesheets(): array
    {
        $stylesheets = $this->getCustom('stylesheet');

        if (!$stylesheets) {
            return [];
        }

        $stylesheets = array_filter($stylesheets, 'strlen');

        asort($stylesheets);

        return array_keys($stylesheets);
    }

    /**
     * Retrieve Scripts
     *
     * @return string[]
     */
    public function getScripts(): array
    {
        $scripts = $this->getCustom('javascript');

        if (!$scripts) {
            return [];
        }

        $scripts = array_filter($scripts, 'strlen');

        asort($scripts);

        return array_keys($scripts);
    }

    /**
     * Retrieve Children blocks
     *
     * @return string[]
     */
    public function getChildren(): array
    {
        $children = $this->getCustom('children');

        if (!$children) {
            return [];
        }

        $children = array_filter($children, 'strlen');

        asort($children);

        return array_keys($children);
    }
}
