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
 * Class Commerce_Helper_Tax
 */
class Commerce_Helper_Tax extends Core_Helper
{
    /**
     * Calculate product prices
     *
     * @param Leafiny_Object $object
     * @param Leafiny_Object|null $address
     *
     * @return void
     */
    public function calculatePrices(Leafiny_Object $object, ?Leafiny_Object $address = null): void
    {
        $originalPrice = $object->getData('price') ?: 0;
        $finalPrice = $object->getData('price') ?: 0;
        $specialPrice = $object->getData('special_price');

        if ($specialPrice) {
            $finalPrice = $specialPrice;
        }

        $default = new Leafiny_Object(
            [
                'original_price' => $originalPrice,
                'final_price'    => $finalPrice,
            ]
        );

        $object->setData('prices_excl_tax', $default);
        $object->setData('prices_incl_tax', $default);
        $object->setData('tax_percent', 0);

        $taxRuleId = $object->getData('tax_rule_id');

        if (!$taxRuleId) {
            return;
        }

        if ($address === null) {
            $address = new Leafiny_Object();
        }

        try {
            $tax = $this->getTaxByAddress($taxRuleId, $address);
            $taxPercent = $tax->getData('tax_percent');

            $object->setData('tax_percent', $taxPercent ?: 0);

            if (!$taxPercent) {
                return;
            }

            $priceType = $object->getData('price_type') ?: 'excl_tax';

            if ($priceType === 'excl_tax') {
                $originalPrice = $originalPrice * (($taxPercent / 100) + 1);
                $finalPrice = $finalPrice * (($taxPercent / 100) + 1);

                $inclTax = new Leafiny_Object(
                    [
                        'original_price' => $originalPrice,
                        'final_price' => $finalPrice,
                    ]
                );
                $object->setData('prices_incl_tax', $inclTax);
            }

            if ($priceType === 'incl_tax') {
                $originalPrice = $originalPrice / (($taxPercent / 100) + 1);
                $finalPrice = $finalPrice / (($taxPercent / 100) + 1);

                $inclTax = new Leafiny_Object(
                    [
                        'original_price' => $originalPrice,
                        'final_price' => $finalPrice,
                    ]
                );
                $object->setData('prices_excl_tax', $inclTax);
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }

    /**
     * Retrieve tax by address
     *
     * @param int            $ruleId
     * @param Leafiny_Object $address
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getTaxByAddress(int $ruleId, Leafiny_Object $address): Leafiny_Object
    {
        $address = $this->getTaxAddress($address);

        $filters = [
            [
                'column' => 'rule_id',
                'value'  => $ruleId,
            ],
            [
                'column'   => 'country_code',
                'value'    => ['*', $address->getData('country_code')],
                'operator' => 'IN'
            ],
            [
                'column'   => 'state_code',
                'value'    => ['*', $address->getData('state_code')],
                'operator' => 'IN'
            ],
        ];

        $postcode = $address->getData('postcode');

        $postcodes = ['*', $postcode];
        $length = strlen($postcode);
        if ($length > 1) {
            for ($i = 1; $i < $length; $i++) {
                $postcodes[] = sprintf('%s*', substr($postcode, 0, -$i));
            }
        }

        $filters[] = [
            'column'   => 'postcode',
            'value'    => $postcodes,
            'operator' => 'IN',
        ];

        $orders = [
            [
                'order' => 'country_code',
                'dir'   => 'DESC'
            ],
            [
                'order' => 'state_code',
                'dir'   => 'DESC'
            ],
            [
                'order' => 'postcode',
                'dir'   => 'DESC'
            ]
        ];

        /** @var Commerce_Model_Tax $model */
        $model = App::getObject('model', 'tax');

        $taxes = $model->getList($filters, $orders, [0, 1]);

        if (empty($taxes)) {
            return new Leafiny_Object();
        }

        return reset($taxes);
    }

    /**
     * Retrieve default tax address
     *
     * @param Leafiny_Object $address
     *
     * @return Leafiny_Object
     */
    public function getTaxAddress(Leafiny_Object $address): Leafiny_Object
    {
        $countryCode = $this->getCustom('default_country_code') ?: '*';
        $stateCode = $this->getCustom('default_state_code') ?: '*';
        $postcode = $this->getCustom('default_postcode') ?: '*';

        return new Leafiny_Object(
            [
                'country_code' => $address->getData('country_code') ?: $countryCode,
                'state_code'   => $address->getData('state_code') ?: $stateCode,
                'postcode'     => $address->getData('postcode') ?: $postcode,
            ]
        );
    }

    /**
     * Count product with specified rule
     *
     * @param int $ruleId
     *
     * @return int
     * @throws Exception
     */
    public function getRuleProductCount(int $ruleId): int
    {
        /** @var Catalog_Model_Product $model */
        $model = App::getObject('model', 'catalog_product');

        $filters = [
            [
                'column' => 'tax_rule_id',
                'value'  => $ruleId,
            ]
        ];

        return $model->size($filters);
    }

    /**
     * Retrieve tax rules
     *
     * @return string[]
     * @throws Exception
     */
    public function getTaxRules(): array
    {
        /** @var Commerce_Model_Tax_Rule $model */
        $model = App::getObject('model', 'tax_rule');

        $ruleIds = [];

        $rules = $model->getList();
        foreach ($rules as $rule) {
            $ruleIds[(int)$rule->getData('rule_id')] = $rule->getData('label');
        }

        return $ruleIds;
    }

    /**
     * Retrieve price types
     *
     * @return string[]
     */
    public function getPriceTypes(): array
    {
        return [
            'excl_tax' => App::translate('Excl. Tax'),
            'incl_tax' => App::translate('Incl. Tax')
        ];
    }
}
