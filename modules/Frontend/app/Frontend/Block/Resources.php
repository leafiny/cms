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

class Frontend_Block_Resources extends Core_Block
{
    /**
     * Retrieve Stylesheets
     *
     * @param Core_Page|null $page
     *
     * @return string[]
     */
    public function getStylesheets(?Core_Page $page = null): array
    {
        $stylesheets = $this->getCustom('stylesheet');

        if (!$stylesheets) {
            return [];
        }

        if ($page !== null) {
            $stylesheets = array_replace($stylesheets, $page->getCustom('stylesheet') ?: []);
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
     * @param Core_Page|null $page
     *
     * @return string[]
     */
    public function getScripts(?Core_Page $page = null): array
    {
        $scripts = $this->getCustom('javascript');

        if (!$scripts) {
            return [];
        }

        if ($page !== null) {
            $scripts = array_replace($scripts, $page->getCustom('javascript') ?: []);
        }

        $scripts = array_filter($scripts, 'strlen');
        asort($scripts);
        $scripts = array_keys($scripts);

        if (!$this->getCustom('merge_js')) {
            foreach ($scripts as $key => $script) {
                $scripts[$key] = $this->getSkinUrl($script);
            }
            return $scripts;
        }

        /** @var Frontend_Helper_Combine_Css $combine */
        $combine = App::getSingleton('helper', 'combine_js');

        return [$combine->mergeResources($scripts)];
    }
}
