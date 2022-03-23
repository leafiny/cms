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
 * Class Commerce_Model_Tax_Rule
 */
class Commerce_Model_Tax_Rule extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_tax_rule';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'rule_id';

    /**
     * Tax validation
     *
     * @param Leafiny_Object $tax
     *
     * @return string
     */
    public function validate(Leafiny_Object $tax): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$tax->getData('label')) {
            return 'The label cannot be empty';
        }

        return '';
    }
}
