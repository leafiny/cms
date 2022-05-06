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
 * Class Commerce_Page_Backend_Order_List
 */
class Commerce_Page_Backend_Order_List extends Backend_Page_Admin_List
{
    /**
     * Retrieve all status
     *
     * @return string[]
     */
    public function getStatuses(): array
    {
        /** @var Commerce_Helper_Order $helperOrder */
        $helperOrder = App::getSingleton('helper', 'order');

        return $helperOrder->getStatuses(App::getLanguage());
    }

    /**
     * Retrieve status label by code
     *
     * @param string|null $code
     *
     * @return string|null
     */
    public function getStatus(?string $code): ?string
    {
        $statuses = $this->getStatuses();

        return isset($statuses[$code]) ? $statuses[$code] : $code;
    }
}
