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
 * Class Commerce_Model_Sale_Status
 */
class Commerce_Model_Sale_Status extends Core_Model
{
    public const SALE_STATUS_PENDING_PAYMENT = 'pending_payment';
    public const SALE_STATUS_PENDING         = 'pending';
    public const SALE_STATUS_PROCESSING      = 'processing';
    public const SALE_STATUS_SHIPPED         = 'shipped';
    public const SALE_STATUS_CANCELED        = 'canceled';
    public const SALE_STATUS_REFUNDED        = 'refunded';

    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_sale_status';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'status_id';

    /**
     * Save
     *
     * @param Leafiny_Object $object
     *
     * @return int
     * @throws Exception
     */
    public function save(Leafiny_Object $object): int
    {
        if ($object->getData('code')) {
            /** @var Core_Helper $helper */
            $helper = App::getObject('helper');
            $object->setData('code', $helper->formatKey($object->getData('code'), [], '_'));
        }

        return parent::save($object);
    }

    /**
     * Retrieve all status
     *
     * @var string $language
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getStatuses(string $language = 'en_US'): array
    {
        $filters = [
            [
                'column' => 'language',
                'value'  => $language,
            ]
        ];

        return parent::getList($filters);
    }

    /**
     * Retrieve status by code
     *
     * @param string $code
     * @param string $language
     *
     * @return Leafiny_Object|null
     * @throws Exception
     */
    public function getByCode(string $code, string $language = 'en_US'): ?Leafiny_Object
    {
        $filters = [
            [
                'column' => 'code',
                'value'  => $code,
            ],
            [
                'column' => 'language',
                'value'  => $language,
            ],
        ];

        $result = parent::getList($filters, [], [0, 1]);

        if (empty($result)) {
            return null;
        }

        return reset($result);
    }

    /**
     * Shipping validation
     *
     * @param Leafiny_Object $object
     *
     * @return string
     */
    public function validate(Leafiny_Object $object): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$object->getData('code')) {
            return 'The status code cannot be empty';
        }

        return '';
    }
}
