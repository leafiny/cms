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
 * Class AddToCart_Page_Product_Ajax_Add
 */
class AddToCart_Page_Product_Ajax_Add extends Core_Page
{
    /**
     * The product to add
     *
     * @var Leafiny_Object|null $product
     */
    protected $product = null;

    /**
     * Execute action
     *
     * @return void
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        try {
            $qty = $this->getPost()->getData('qty') ?: 1;
            $product = $this->getProduct();

            App::dispatchEvent('cart_add_product_action_before', ['page' => $this, 'product' => $product]);

            if ($product->getData('product_id')) {
                /** @var Commerce_Helper_Cart $helper */
                $helper = App::getSingleton('helper', 'cart');
                $item = $helper->addProduct($product, (int)$qty);

                $this->setData('item', $item);

                App::dispatchEvent(
                    'cart_add_product_action_after',
                    ['page' => $this, 'product' => $product, 'item' => $item]
                );
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        $this->responseJson();
    }

    /**
     * Send json data
     *
     * @return void
     */
    protected function responseJson(): void
    {
        header('Content-Type: application/json');
        $data = [];
        try {
            $data['minicart'] = $this->formatContent($this->renderBlock('mini.cart'));
            $data['popup'] = $this->formatContent(
                $this->renderBlock(
                    'ajax.cart.popup',
                    [
                        'product' => $this->getProduct(),
                        'item'    => $this->getData('item'),
                    ]
                )
            );
        } catch (Throwable $throwable) {
            $data['error'] = $throwable->getMessage();
            App::log($throwable, Core_Interface_Log::ERR);
        }

        $this->postRender();
        print json_encode($data);
        exit;
    }

    /**
     * Retrieve product id
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    protected function getProduct(): Leafiny_Object
    {
        if ($this->product !== null) {
            return $this->product;
        }

        /** @var Core_Helper_Crypt $encrypt */
        $encrypt = App::getObject('helper_crypt');

        $productId = (int)$encrypt->decrypt((string)$this->getObjectKey());

        if (!$productId) {
            throw new Exception(App::translate('The product does not exist'));
        }

        /** @var Catalog_Model_Product $model */
        $model = App::getObject('model', 'catalog_product');

        $product = $model->get((int)$productId);

        if (!$product->getData('product_id')) {
            throw new Exception(App::translate('The product does not exist'));
        }

        $this->product = $product;

        return $product;
    }

    /**
     * Strip HTML comments
     *
     * @param string $html
     *
     * @return string
     */
    protected function formatContent(string $html): string
    {
        $html = preg_replace('/<!--(.*)-->/Uis', '', $html);
        $html = preg_replace('/>\s+</', '><', $html);

        return $html;
    }
}
