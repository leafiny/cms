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
 * Class Backend_Page_Dashboard
 */
class Backend_Page_Dashboard extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Categories
     *
     * @var null $categories
     */
    protected $categories = [];

    /**
     * Retrieve categories
     *
     * @return array
     * @throws Exception
     */
    public function getCategories(): array
    {
        if (!empty($this->categories)) {
            return $this->categories;
        }

        /** @var Backend_Block_Menu $menu */
        $menu = App::getObject('block', 'admin.menu');
        $this->categories = $menu->getTree();

        return $this->categories;
    }

    /**
     * Retrieve color from key
     *
     * @param int $key
     *
     * @return string
     */
    public function getColor(int $key): string
    {
        /** @var Backend_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'admin_data');

        return $helper->getColor($key);
    }
}
