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
 * Class Commerce_Helper_Shipping
 */
class Commerce_Helper_Shipping extends Core_Helper
{
    /**
     * Retrieve shipping method with prices
     *
     * @param string              $method
     * @param float               $weight
     * @param Leafiny_Object|null $address
     * @param Leafiny_Object|null $sale
     * @param bool|null           $estimate
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getMethod(
        string $method,
        float $weight = 0,
        ?Leafiny_Object $address = null,
        ?Leafiny_Object $sale = null,
        ?bool $estimate = false
    ): Leafiny_Object {
        /** @var Commerce_Model_Shipping $model */
        $model = App::getObject('model', 'shipping');
        $method = $model->get($method, 'method');

        $weight = $weight + $this->getPackageWeight();

        if (!$method->getData('shipping_id')) {
            return $method;
        }

        foreach ($method->getData('prices') as $price) {
            if ($weight >= $price['weight_from']) {
                $method->setData('price', $price['price']);
            }
        }

        if ($estimate) {
            $method->setData('_estimate', true);
        }

        /** @var Commerce_Helper_Tax $taxHelper */
        $taxHelper = App::getSingleton('helper', 'tax');
        $taxHelper->calculatePrices($method, $address);

        App::dispatchEvent('sale_shipping_method', ['method' => $method, 'sale' => $sale, 'address' => $address]);

        return $method;
    }

    /**
     * Check method is valid for address
     *
     * @param string         $methodName
     * @param Leafiny_Object $address
     *
     * @return bool
     * @throws Exception
     */
    public function isMethodValidForAddress(string $methodName, Leafiny_Object $address): bool
    {
        $methods = $this->getMethodsByAddress($address);

        foreach ($methods as $method) {
            if ($method->getData('method') === $methodName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve package weight
     *
     * @return float
     */
    public function getPackageWeight(): float
    {
        return (float)$this->getCustom('package_weight');
    }

    /**
     * Assign shipping method to sale
     *
     * @param int         $saleId
     * @param string|null $code
     *
     * @return void
     */
    public function assignShippingMethod(int $saleId, ?string $code = null): void
    {
        try {
            /** @var Commerce_Model_Sale_Address $model */
            $model = App::getSingleton('model', 'sale_address');
            $address = $model->getBySaleId($saleId, 'shipping');
            $candidate = null;

            if ($address) {
                $methods = $this->getMethodsByAddress($address);
                foreach ($methods as $method) {
                    if ($code === null) {
                        $candidate = $method->getData('method');
                        break;
                    }
                    if ($method->getData('method') !== $code) {
                        continue;
                    }
                    $candidate = $code;
                }
            }

            /** @var Commerce_Model_Sale $model */
            $model = App::getSingleton('model', 'sale');
            $model->save(new Leafiny_Object([
                'sale_id'         => $saleId,
                'shipping_method' => $candidate
            ]));
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }

    /**
     * Retrieve shipping methods by address
     *
     * @param Leafiny_Object $address
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getMethodsByAddress(Leafiny_Object $address): array
    {
        $filters = [
            [
                'column'   => 'countries',
                'value'    => ['*', $address->getData('country_code')],
                'operator' => 'FIND_IN_SET'
            ],
            [
                'column'   => 'states',
                'value'    => ['*', $address->getData('state_code')],
                'operator' => 'FIND_IN_SET'
            ],
        ];

        $postcode = $address->getData('postcode') ?: '';

        $postcodes = ['*', $postcode];
        $length = strlen($postcode);
        if ($length > 1) {
            for ($i = 1; $i < $length; $i++) {
                $postcodes[] = sprintf('%s*', substr($postcode, 0, -$i));
            }
        }

        $filters[] = [
            'column'   => 'postcodes',
            'value'    => $postcodes,
            'operator' => 'FIND_IN_SET',
        ];

        $orders = [
            [
                'order' => 'priority',
                'dir'   => 'ASC'
            ],
        ];

        /** @var Commerce_Model_Shipping $model */
        $model = App::getObject('model', 'shipping');

        return $model->getList($filters, $orders);
    }

    /**
     * Retrieve method prices for a sale
     *
     * @param int    $saleId
     * @param string $method
     *
     * @return Leafiny_Object
     */
    public function getMethodPrices(int $saleId, string $method): Leafiny_Object
    {
        /** @var Commerce_Helper_Shipping $shippingHelper */
        $shippingHelper = App::getSingleton('helper', 'shipping');
        /** @var Commerce_Helper_Cart $cartHelper */
        $cartHelper = App::getSingleton('helper', 'cart');

        $prices = new Leafiny_Object(
            [
                'incl_tax_shipping'          => 0,
                'excl_tax_shipping'          => 0,
                'original_incl_tax_shipping' => 0,
                'original_excl_tax_shipping' => 0,
            ]
        );

        try {
            $sale = $cartHelper->getSale($saleId);
            if (!$sale) {
                return $prices;
            }

            $method = $shippingHelper->getMethod(
                $method,
                (float)$sale->getData('total_weight'),
                $cartHelper->getAddress('shipping', $sale),
                $sale,
                true
            );

            $prices->setData(
                [
                    'incl_tax_shipping'          => $method->getData('prices_incl_tax')->getData('final_price'),
                    'excl_tax_shipping'          => $method->getData('prices_excl_tax')->getData('final_price'),
                    'original_incl_tax_shipping' => $method->getData('prices_incl_tax')->getData('original_price'),
                    'original_excl_tax_shipping' => $method->getData('prices_excl_tax')->getData('original_price'),
                ]
            );
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $prices;
    }

    /**
     * Retrieve allowed countries
     *
     * @return string[]
     */
    public function getAllowedCountries(): array
    {
        return $this->getCustom('allowed_countries') ?: [];
    }

    /**
     * Retrieve default country code
     *
     * @return string
     */
    public function getDefaultCountryCode(): string
    {
        return $this->getCustom('default_country_code') ?: '';
    }

    /**
     * Retrieve default state code
     *
     * @return string
     */
    public function getDefaultStateCode(): string
    {
        return $this->getCustom('default_state_code') ?: '';
    }

    /**
     * Retrieve default postcode
     *
     * @return string
     */
    public function getDefaultPostcode(): string
    {
        return $this->getCustom('default_postcode') ?: '';
    }

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
