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
 * Class Commerce_Page_Order_Complete
 */
class Commerce_Page_Order_Complete extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $sale = $this->getSale();

        if (!$sale->getData('sale_id')) {
            $this->redirect();
        }

        $this->setTmpSessionData('last_sale_id', $sale->getData('sale_id'));
        $this->setSale($sale);

        App::dispatchEvent('checkout_order_complete_action', ['sale' => $sale]);
    }

    /**
     * Reassign sale id to session if needed (payment failed, canceled...)
     *
     * @param Leafiny_Object $sale
     */
    protected function setSale(Leafiny_Object $sale): void
    {
        $session = App::getSession(Core_Template_Abstract::CONTEXT_DEFAULT);
        if ($session) {
            $session->set('sale_id', null);
            if ($sale->getData('state') === Commerce_Model_Sale::SALE_STATE_CART) {
                $session->set('sale_id', $sale->getData('sale_id'));
            }
        }
    }

    /**
     * Retrieve sale
     *
     * @return Leafiny_Object
     */
    protected function getSale(): Leafiny_Object
    {
        /** @var Commerce_Model_Sale $saleModel */
        $saleModel = App::getSingleton('model', 'sale');

        $params = $this->getParams();
        $key = $params->getData('key');

        $saleId = $this->getTmpSessionData('last_sale_id');

        $sale = new Leafiny_Object();

        try {
            if ($saleId) {
                $sale = $saleModel->get((int)$saleId);
            }

            if (!$saleId && $key) {
                $sale = $saleModel->get($key, 'key');
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $sale;
    }
}
