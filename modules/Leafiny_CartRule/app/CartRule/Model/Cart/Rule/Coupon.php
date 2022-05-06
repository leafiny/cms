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
 * Class CartRule_Model_Cart_Rule_Coupon
 */
class CartRule_Model_Cart_Rule_Coupon extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_cart_rule_coupon';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'coupon_id';

    /**
     * Delete all the coupons for the rule
     *
     * @param int $ruleId
     *
     * @return bool
     * @throws Exception
     */
    public function deleteAll(int $ruleId): bool
    {
        if ($this->getMainTable() === null) {
            return false;
        }
        if ($this->getPrimaryKey() === null) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('rule_id', $ruleId);
        $result = $adapter->delete($this->getMainTable());

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        return $result;
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

        if (!$tax->getData('rule_id')) {
            return 'The rule cannot be empty';
        }
        if (!$tax->getData('code')) {
            return 'The code cannot be empty';
        }

        return '';
    }
}
