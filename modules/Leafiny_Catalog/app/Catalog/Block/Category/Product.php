<?php

declare(strict_types=1);

/**
 * Class Catalog_Block_Category_Product
 */
class Catalog_Block_Category_Product extends Core_Block
{
    /**
     * Retrieve products
     *
     * @param int $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getProducts(int $categoryId): array
    {
        /** @var Catalog_Model_Product $model */
        $model = App::getObject('model', 'catalog_product');

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

    /**
     * Retrieve product main image
     *
     * @param int $productId
     *
     * @return Leafiny_Object|null
     */
    public function getMainImage(int $productId): ?Leafiny_Object
    {
        /** @var Gallery_Model_Image $image */
        $image = App::getObject('model', 'gallery_image');

        try {
            return $image->getMainImage($productId, 'catalog_product');
        } catch (Exception $exception) {
            /** @var Log_Model_File $log */
            $log = App::getObject('model', 'log_file');
            $log->add($exception->getMessage());
        }

        return null;
    }
}
