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
 * Class Cms_Block_Search_Pages
 */
class Cms_Block_Search_Pages extends Core_Block
{
    /**
     * Retrieve pages
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getPages(): array
    {
        if (!$this->getPageIds()) {
            return [];
        }

        /** @var Cms_Helper_Cms_Page $helper */
        $helper = App::getSingleton('helper', 'cms_page');
        $helper->setCustom(
            'filters',
            [
                'page_id' => [
                    'column'   => 'page_id',
                    'value'    => $this->getPageIds(),
                    'operator' => 'IN',
                ],
            ]
        );

        $orders = [
            [
                'order'  => 'page_id',
                'dir'    => 'ASC',
                'custom' => $this->getPageIds(),
            ]
        ];

        /** @var Cms_Model_Page $model */
        $model = App::getObject('model', 'cms_page');

        return $model->getList($helper->getFilters(), $orders, $this->getLimit());
    }

    /**
     * Retrieve limit
     *
     * @return int[]
     */
    public function getLimit(): array
    {
        return $this->getCustom('limit') ?? [0, 100];
    }

    /**
     * Retrieve object Ids
     *
     * @return array
     */
    public function getPageIds(): array
    {
        if (!$this->getResult()) {
            return [];
        }

        return $this->getResult()->getData('response') ?? [];
    }

    /**
     * Retrieve search result
     *
     * @return Leafiny_Object|null
     */
    public function getResult(): ?Leafiny_Object
    {
        return $this->getCustom('result');
    }
}
