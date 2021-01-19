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
 * Class Backend_Block_Menu
 */
class Backend_Block_Menu extends Core_Block
{
    /**
     * Menu tree
     *
     * @var null $tree
     */
    protected $tree = [];

    /**
     * Retrieve menu tree
     *
     * @return array
     * @throws Exception
     */
    public function getTree(): array
    {
        if (!empty($this->tree)) {
            return $this->tree;
        }

        $tree = $this->getCustom('tree');

        if (!$tree) {
            return $this->tree;
        }

        ksort($tree);

        foreach ($tree as $position => $data) {
            foreach ($data as $title => $children) {
                ksort($children);
                foreach ($children as $key => $child) {
                    if (!$this->isAllowed($child['path'])) {
                        unset($children[$key]);
                    }
                }
                if (!empty($children)) {
                    $this->tree[$title] = $children;
                }
            }
        }

        return $this->tree;
    }

    /**
     * Retrieve if menu is active
     *
     * @param Core_Page $page
     * @param string    $path
     *
     * @return bool
     */
    public function isActive(Core_Page $page, string $path): bool
    {
        if ($page->getCustom('active_menu') === $path) {
            return true;
        }

        if ($page->getCustom('referer_identifier') === $path) {
            return true;
        }

        if ($page->getObjectIdentifier() === $path) {
            return true;
        }

        return false;
    }

    /**
     * Check if resource is allowed for current admin user
     *
     * @param string $resource
     *
     * @return bool
     * @throws Exception
     */
    public function isAllowed(string $resource): bool
    {
        /** @var Backend_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'admin_data');

        return $helper->isAllowed($resource);
    }
}
