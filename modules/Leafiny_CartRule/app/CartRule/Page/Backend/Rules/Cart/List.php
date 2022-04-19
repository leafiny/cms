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
 * Class CartRule_Page_Backend_Rules_Cart_List
 */
class CartRule_Page_Backend_Rules_Cart_List extends Backend_Page_Admin_List
{
    /**
     * Retrieve type label
     *
     * @param string|null $code
     *
     * @return string
     */
    public function getTypeLabel(?string $code): string
    {
        $types = $this->getCartRuleTypes();

        return $types[$code] ?? '';
    }

    /**
     * Retrieve cart rules
     *
     * @return string[]
     */
    public function getCartRuleTypes(): array
    {
        /** @var CartRule_Helper_Cart_Rule $helper */
        $helper = App::getSingleton('helper', 'cart_rule');

        return call_user_func_array('array_merge', $helper->getCartRuleAllowedTypes());
    }
}
