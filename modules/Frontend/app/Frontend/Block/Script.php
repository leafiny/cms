<?php

declare(strict_types=1);

/**
 * Class Frontend_Block_Script
 */
class Frontend_Block_Script extends Core_Block
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

        ksort($scripts);

        return $scripts;
    }
}
