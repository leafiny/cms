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
 * Class Commerce_Observer_Sale_InitAddress
 */
class Commerce_Observer_Sale_InitAddress extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Init shipping and billing addresses
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $sale */
        $sale = $object->getData('object');
        /** @var Commerce_Model_Sale_Address $model */
        $model = App::getSingleton('model', 'sale_address');

        try {
            $exists = $model->size([
                [
                    'column' => 'sale_id',
                    'value'  => $sale->getData('sale_id'),
                ]
            ]);

            if (!$exists) {
                $types = ['shipping', 'billing'];

                foreach ($types as $type) {
                    $data = [
                        'sale_id' => $sale->getData('sale_id'),
                        'type'    => $type,
                    ];

                    $model->save(new Leafiny_Object($data));
                }
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }
}