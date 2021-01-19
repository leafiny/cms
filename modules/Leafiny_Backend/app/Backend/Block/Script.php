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
 * Class Backend_Block_Script
 */
class Backend_Block_Script extends Core_Block
{
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

        return array_keys($scripts);
    }
}
