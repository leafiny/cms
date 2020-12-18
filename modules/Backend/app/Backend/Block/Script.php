<?php

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

        $scripts = array_filter($scripts);

        ksort($scripts);

        return $scripts;
    }
}
