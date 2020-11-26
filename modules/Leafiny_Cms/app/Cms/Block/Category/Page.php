<?php

declare(strict_types=1);

/**
 * Class Cms_Block_Category_Page
 */
class Cms_Block_Category_Page extends Core_Block
{
    /**
     * Retrieve pages
     *
     * @param int $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getPages(int $categoryId): array
    {
        /** @var Cms_Model_Page $model */
        $model = App::getObject('model', 'cms_page');

        $filters = [
            [
                'column'   => 'status',
                'value'    => 1,
                'operator' => '=',
            ],
        ];

        $orders = [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ]
        ];

        return $model->addCategoryFilter($categoryId)->getList($filters, $orders);
    }
}
