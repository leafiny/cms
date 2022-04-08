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
 * Class Commerce_Observer_Cart_Rule_RefreshShipping
 */
class Commerce_Observer_Cart_Rule_RefreshShipping extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Gift specific data
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $item */
        $method = $object->getData('method');
        /** @var Leafiny_Object $sale */
        $sale = $object->getData('sale');

        if ($sale) {
            /** @var Commerce_Helper_Cart_Rule $helperCartRule */
            $helperCartRule = App::getObject('helper', 'cart_rule');

            if ($method->getData('_estimate')) {
                try {
                    /** @var Commerce_Model_Cart_Rule $ruleModel */
                    $ruleModel = App::getObject('model', 'cart_rule');
                    $filters = [
                        [
                            'column' => 'type',
                            'value'  => Commerce_Model_Cart_Rule::TYPE_PERCENT_SHIPPING,
                        ],
                        [
                            'column' => 'has_coupon',
                            'value'  => 0,
                        ]
                    ];
                    $rules = $ruleModel->getList($filters);

                    $shippingRuleIds = [];
                    foreach ($rules as $rule) {
                        $shippingRuleIds[] = (int)$rule->getData('rule_id');
                    }

                    $sale->setData('rule_ids', join(',', $shippingRuleIds));
                    $sale->setData('shipping_method', $method->getData('method'));
                    $sale->setData('_keep_rules', true);
                } catch (Throwable $throwable) {
                    App::log($throwable, Core_Interface_Log::ERR);
                }
            }

            $rate = $helperCartRule->getShippingDiscountRate($sale);

            $priceInclTax = $method->getData('prices_incl_tax')->getData('final_price') * $rate;
            $priceExclTax = $method->getData('prices_excl_tax')->getData('final_price') * $rate;

            $method->getData('prices_incl_tax')->setData('final_price', $priceInclTax);
            $method->getData('prices_excl_tax')->setData('final_price', $priceExclTax);
        }
    }
}
