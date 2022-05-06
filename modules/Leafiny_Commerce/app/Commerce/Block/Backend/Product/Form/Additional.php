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
 * Class Commerce_Block_Backend_Product_Form_Additional
 */
class Commerce_Block_Backend_Product_Form_Additional extends Core_Block
{
    /**
     * Retrieve tax rules
     *
     * @return string[]
     * @throws Exception
     */
    public function getTaxRules(): array
    {
        /** @var Commerce_Helper_Tax $helper */
        $helper = App::getSingleton('helper', 'tax');

        return $helper->getTaxRules();
    }

    /**
     * Retrieve price types
     *
     * @return string[]
     */
    public function getPriceTypes(): array
    {
        /** @var Commerce_Helper_Tax $helper */
        $helper = App::getSingleton('helper', 'tax');

        return $helper->getPriceTypes();
    }

    /**
     * Retrieve default price type
     *
     * @return string
     */
    public function getDefaultPriceType(): string
    {
        /** @var Commerce_Helper_Product $helper */
        $helper = App::getSingleton('helper', 'commerce_product');

        return $helper->getDefaultPriceType();
    }

    /**
     * Retrieve default tax rule id
     *
     * @return int
     */
    public function getDefaultTaxRuleId(): int
    {
        /** @var Commerce_Helper_Product $helper */
        $helper = App::getSingleton('helper', 'commerce_product');

        return $helper->getDefaultTaxRuleId();
    }
}
