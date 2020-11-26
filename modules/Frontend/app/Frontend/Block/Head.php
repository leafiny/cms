<?php

declare(strict_types=1);

/**
 * Class Frontend_Block_Head
 */
class Frontend_Block_Head extends Core_Block
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
}
