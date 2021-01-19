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
 * Class Backend_Page_Cache_List
 */
class Backend_Page_Cache_List extends Backend_Page_Admin_List
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        /** @var Backend_Model_Cache $model */
        $model = App::getObject('model', 'admin_cache');

        $this->list = $model->get();

        parent::action();
    }

    /**
     * Retrieve Children blocks
     *
     * @return string[]
     */
    public function getChildren(): array
    {
        $children = $this->getCustom('children');

        if (!$children) {
            return [];
        }

        ksort($children);

        return $children;
    }
}
