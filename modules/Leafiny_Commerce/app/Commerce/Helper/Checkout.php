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
 * Class Commerce_Helper_Checkout
 */
class Commerce_Helper_Checkout extends Core_Helper
{
    /**
     * Is step available
     *
     * @param string              $code
     * @param Leafiny_Object|null $sale
     *
     * @return bool
     */
    public function isStepAvailable(string $code, ?Leafiny_Object $sale = null): bool
    {
        $steps = $this->getSteps($sale);

        foreach ($steps as $step) {
            if ($step->getData('code') === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve all steps
     *
     * @return Leafiny_Object[]
     */
    public function getSteps(?Leafiny_Object $sale = null): array
    {
        $config = $this->getCustom('steps') ?: [];
        $steps = [];

        foreach ($config as $code => $step) {
            if (!isset($code, $step['label'])) {
                continue;
            }
            $data = new Leafiny_Object($step);
            $data->setData('code', $code);

            App::dispatchEvent('checkout_step', ['step' => $data, 'sale' => $sale]);

            if (!$data->getData('enabled')) {
                continue;
            }

            $steps[(int)$data->getData('position')] = $data;
        }
        ksort($steps);

        return array_values($steps);
    }

    /**
     * Retrieve next step URL
     *
     * @param string|null $stepCode
     *
     * @return string
     */
    public function getStepUrl(?string $stepCode = null): string
    {
        $checkoutUrl = '/checkout.html';

        if (!$stepCode) {
            return $checkoutUrl;
        }

        return $checkoutUrl . '?step=' . $stepCode . '#' . $stepCode;
    }
}
