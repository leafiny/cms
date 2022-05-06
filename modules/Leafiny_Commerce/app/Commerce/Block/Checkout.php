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
 * Class Commerce_Block_Checkout
 */
class Commerce_Block_Checkout extends Core_Block
{
    /**
     * The current sale
     *
     * @var Leafiny_Object|null
     */
    protected $currentSale = null;

    /**
     * Set custom data
     * Assign step number from step code
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Core_Template_Abstract
     */
    public function setCustom(string $key, $value): Core_Template_Abstract
    {
        parent::setCustom($key, $value);
        if ($key !== 'step' || $this->getData('step_position') !== null) {
            return $this;
        }

        $steps = $this->getSteps();
        $this->setData('step_position', 0);
        foreach ($steps as $number => $step) {
            if ($step->getData('code') === $value) {
                $this->setData('step_position', $number);
            }
        }

        return $this;
    }

    /**
     * Retrieve current step position
     *
     * @return int
     */
    public function getStepPosition(): int
    {
        return (int)$this->getData('step_position');
    }

    /**
     * Retrieve all steps
     *
     * @return Leafiny_Object[]
     */
    public function getSteps(): array
    {
        /** @var Commerce_Helper_Checkout $helper */
        $helper = $this->getHelper('checkout');

        return $helper->getSteps($this->getSale());
    }

    /**
     * Retrieve current step
     *
     * @return Leafiny_Object
     */
    public function getCurrentStep(): Leafiny_Object
    {
        return $this->getSteps()[$this->getStepPosition()];
    }

    /**
     * Retrieve next step
     *
     * @return Leafiny_Object|null
     */
    public function getNextStep(): ?Leafiny_Object
    {
        $key = $this->getStepPosition() + 1;

        return isset($this->getSteps()[$key]) ? $this->getSteps()[$key] : null;
    }

    /**
     * Retrieve previous step
     *
     * @return Leafiny_Object|null
     */
    public function getPreviousStep(): ?Leafiny_Object
    {
        $key = $this->getStepPosition() - 1;

        return isset($this->getSteps()[$key]) ? $this->getSteps()[$key] : null;
    }

    /**
     * Retrieve next step URL
     *
     * @param string|null $stepCode
     *
     * @return string
     */
    public function getStepUrl(?string $stepCode): string
    {
        /** @var Commerce_Helper_Checkout $helper */
        $helper = $this->getHelper('checkout');

        return $this->getUrl($helper->getStepUrl($stepCode));
    }

    /**
     * Check if cart is empty
     *
     * @return bool
     */
    public function isCartEmpty(): bool
    {
        return empty($this->getItems());
    }

    /**
     * Retrieve cart items
     *
     * @return Leafiny_Object[]
     */
    public function getItems(): array
    {
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        return $helper->getItems();
    }

    /**
     * Retrieve current sale
     *
     * @return Leafiny_Object|null
     */
    public function getSale(): Leafiny_Object
    {
        if ($this->currentSale !== null) {
            return $this->currentSale;
        }

        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');
        $this->currentSale = new Leafiny_Object();

        try {
            $current = $helper->getCurrentSale();
            if ($current) {
                $this->currentSale = $helper->getCurrentSale();
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $this->currentSale;
    }
}
