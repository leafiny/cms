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
 * Class Commerce_Helper_Product
 */
class Commerce_Helper_Product extends Core_Helper
{
    /**
     * Retrieve default price type
     *
     * @return string
     */
    public function getDefaultPriceType(): string
    {
        return (string)$this->getCustom('default_price_type');
    }

    /**
     * Retrieve default tax rule id
     *
     * @return int
     */
    public function getDefaultTaxRuleId(): int
    {
        return (int)$this->getCustom('default_tax_rule_id');
    }
}
