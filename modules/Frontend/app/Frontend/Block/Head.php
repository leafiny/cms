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
        $stylesheets = array_keys($stylesheets);

        if (!$this->getCustom('merge_css')) {
            foreach ($stylesheets as $key => $stylesheet) {
                $stylesheets[$key] = $this->getSkinUrl($stylesheet);
            }
            return $stylesheets;
        }

        /** @var Frontend_Helper_Combine_Css $combine */
        $combine = App::getSingleton('helper', 'combine_css');

        return [$combine->mergeResources($stylesheets)];
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
