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
 * Class Backend_Observer_SetLocaleInformation
 */
class Backend_Observer_SetLocaleInformation extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Backend_Page_Admin_Page_Abstract $page */
        $page = $object->getData('object');

        if (!$page->canProcess()) {
            return;
        }

        /** @var Core_Page $page */
        $page = $object->getData('object');

        if ($page->getCustom('locale')) {
            setlocale(LC_ALL, $page->getCustom('locale'));
        }
        if ($page->getCustom('timezone')) {
            date_default_timezone_set($page->getCustom('timezone'));
        }
    }
}
