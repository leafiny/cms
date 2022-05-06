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
 * Class Commerce_Page_Checkout
 */
class Commerce_Page_Checkout extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $steps = $this->getSteps();
        if (empty($steps)) {
            $this->redirect();
        }

        $stepCode = $this->getStepCode();

        if (!$this->getData('checkout_action_no_process')) {
            foreach ($steps as $key => $step) {
                if ($stepCode === $step->getData('code')) {
                    if (isset($steps[$key - 1])) {
                        App::dispatchEvent(
                            'checkout_action_save_' . $steps[$key - 1]->getData('code'),
                            ['checkout' => $this, 'step' => $steps[$key - 1]]
                        );
                    }
                    App::dispatchEvent(
                        'checkout_action_step_' . $step->getData('code'),
                        ['checkout' => $this, 'step' => $step]
                    );
                    break;
                }
            }
        }

        if (!$this->getData('checkout_action_no_validate')) {
            foreach ($steps as $step) {
                if ($stepCode === $step->getData('code')) {
                    break;
                }
                App::dispatchEvent(
                    'checkout_action_validate_' . $step->getData('code'),
                    ['checkout' => $this, 'step' => $step]
                );
            }
        }

        if ($this->isAjax()) {
            $this->responseJson($stepCode);
        }
    }

    /**
     * Send json data
     *
     * @param string $stepCode
     */
    public function responseJson(string $stepCode): void
    {
        header('Content-Type: application/json');
        $data = ['redirect' => '', 'error' => false, 'step' => $stepCode, 'content' => ''];
        if ($this->isCartEmpty()) {
            $data['error'] = true;
            $data['redirect'] = $this->getUrl('/checkout.html');
        } else {
            try {
                $identifier = 'commerce.checkout.' . $stepCode;
                $stepContent = $this->renderBlock($identifier, ['step' => $stepCode]);
                $data['content'] = preg_replace('/>\s+</', '><', $this->renderBlock('message') . $stepContent);
                if ($this->getErrorMessages()) {
                    $data['error'] = true;
                }
            } catch (Throwable $throwable) {
                $data['error'] = true;
                $data['content'] = $throwable->getMessage();
                App::log($throwable, Core_Interface_Log::ERR);
            }
        }
        $this->postRender();
        print json_encode($data);
        exit;
    }

    /**
     * Is an ajax request
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return (bool)$this->getParams()->getData('is_ajax');
    }

    /**
     * Retrieve all steps
     *
     * @return Leafiny_Object[]
     */
    public function getSteps(): array
    {
        /** @var Commerce_Helper_Checkout $helperCheckout */
        $helperCheckout = $this->getHelper('checkout');
        /** @var Commerce_Helper_Cart $helperCart */
        $helperCart = App::getSingleton('helper', 'cart');

        try {
            return $helperCheckout->getSteps($helperCart->getCurrentSale());
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }

    /**
     * Retrieve current step. Set first step if empty.
     *
     * @return string
     */
    public function getStepCode(): string
    {
        /** @var Commerce_Helper_Checkout $helperCheckout */
        $helperCheckout = $this->getHelper('checkout');
        /** @var Commerce_Helper_Cart $helperCart */
        $helperCart = App::getSingleton('helper', 'cart');

        $params = $this->getParams();
        $step = $params->getData('step');

        try {
            if (!$step || !$helperCheckout->isStepAvailable($step, $helperCart->getCurrentSale())) {
                $steps = $this->getSteps();
                if (isset($steps[0])) {
                    return $steps[0]->getData('code');
                }
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return strtolower((string)$step);
    }

    /**
     * Retrieve cart items
     *
     * @return bool
     */
    public function isCartEmpty(): bool
    {
        /** @var Commerce_Model_Sale_Item $model */
        $model = App::getObject('model', 'sale_item');
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        try {
            if ($helper->getCurrentId()) {
                return empty($model->getItems($helper->getCurrentId()));
            }
        } catch (Throwable $throwable) {
            return true;
        }

        return true;
    }
}
