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
 * Class Commerce_Observer_Checkout_Shipping_View
 */
class Commerce_Observer_Checkout_Shipping_View extends Commerce_Observer_Checkout_Process
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        parent::execute($object);

        $this->getCheckout()->setCustom(
            'meta_title',
            App::translate('Checkout') . ' - ' . App::translate($this->getStep()->getData('label'))
        );
    }
}
