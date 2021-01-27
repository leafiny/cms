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
}
