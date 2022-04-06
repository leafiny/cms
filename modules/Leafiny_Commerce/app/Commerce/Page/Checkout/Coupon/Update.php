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
 * Class Commerce_Page_Checkout_Coupon_Update
 */
class Commerce_Page_Checkout_Coupon_Update extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $params = $this->getPost();

        /** @var Commerce_Helper_Cart $cartHelper */
        $cartHelper = App::getSingleton('helper', 'cart');
        /** @var Commerce_Helper_Cart_Rule $cartRuleHelper */
        $cartRuleHelper = App::getSingleton('helper', 'cart_rule');

        $code = $params->getData('coupon');

        try {
            $sale = $cartHelper->getCurrentSale();

            if (!$sale) {
                $this->redirect($this->getRefererUrl());
            }

            if (!$code) {
                $cartRuleHelper->removeCartRule($sale->getData('coupon_rule_id'), $sale);
                $cartHelper->calculation();
                $this->redirect($this->getRefererUrl());
            }

            /** @var Commerce_Model_Cart_Rule_Coupon $couponModel */
            $couponModel = App::getSingleton('model', 'cart_rule_coupon');
            $coupon = $couponModel->get($code, 'code');

            if (!$coupon->getData('coupon_id')) {
                $this->setErrorMessage(App::translate('This coupon does not exist'));
                $this->redirect($this->getRefererUrl());
            }

            if (!$cartRuleHelper->addCartRule((int)$coupon->getData('rule_id'), $coupon)) {
                $this->setErrorMessage(App::translate('This coupon is not longer available'));
                $this->redirect($this->getRefererUrl());
            }

            $cartHelper->calculation();
            $this->setSuccessMessage(App::translate('The coupon has been applied'));
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        $this->redirect($this->getRefererUrl());
    }

    /**
     * Retrieve referer Url
     *
     * @return string|null
     */
    public function getRefererUrl(): ?string
    {
        /** @var Commerce_Helper_Checkout $helperCheckout */
        $helperCheckout = $this->getHelper('checkout');

        return $this->getUrl($helperCheckout->getStepUrl('review'));
    }
}
