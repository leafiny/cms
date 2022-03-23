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
 * Class Commerce_Model_Tax
 */
class Commerce_Model_Tax extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_tax';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'tax_id';

    /**
     * Save
     *
     * @param Leafiny_Object $object
     *
     * @return int|null
     * @throws Exception
     */
    public function save(Leafiny_Object $object): ?int
    {
        if ($object->getData('tax_percent')) {
            $object->setData('tax_percent', (float)$object->getData('tax_percent'));
        }
        if (!$object->getData('state_code')) {
            $object->setData('state_code', '*');
        }
        if (!$object->getData('postcode')) {
            $object->setData('postcode', '*');
        }

        return parent::save($object);
    }

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
        if (!$tax->getData('country_code')) {
            return 'The country cannot be empty';
        }

        return '';
    }
}
