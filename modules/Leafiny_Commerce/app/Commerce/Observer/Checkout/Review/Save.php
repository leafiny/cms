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
 * Class Commerce_Observer_Checkout_Review_Save
 */
class Commerce_Observer_Checkout_Review_Save extends Commerce_Observer_Checkout_Process
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        parent::execute($object);

        $post = $this->getCheckout()->getPost();

        if (!$post->hasData()) {
            return;
        }

        if (!$this->getCurrentSale()->getData('sale_id')) {
            return;
        }

        if (!$post->getData('agreements')) {
            $this->error('Please accept the general conditions of sale');
            return;
        }

        try {
            $sale = $this->getCurrentSale();
            $sale->setData('agreements', 1);

            /** @var Commerce_Model_Sale $saleModel */
            $saleModel = App::getSingleton('model', 'sale');
            $saleModel->save($sale);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->error('An error occurred with the cart. Please contact us.');
        }
    }
}
