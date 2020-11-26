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

        ksort($stylesheets);

        return $stylesheets;
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

        ksort($scripts);

        return $scripts;
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

        ksort($children);

        return $children;
    }
}
