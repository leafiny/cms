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
 * Class Commerce_Block_AddToCart
 */
class Commerce_Block_AddToCart extends Core_Block
{
    /**
     * Retrieve add to cart URL
     *
     * @param Leafiny_Object $product
     *
     * @return string
     */
    public function getAddUrl(Leafiny_Object $product): string
    {
        return $this->getUrl('/product/add/' . $this->crypt((string)$product->getData('product_id')) . '/');
    }

    /**
     * Retrieve button label
     *
     * @return string
     */
    public function getButtonLabel(): string
    {
        return $this->getCustom('button_label') ?: App::translate('Add to cart');
    }
}
