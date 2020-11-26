<?php

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
