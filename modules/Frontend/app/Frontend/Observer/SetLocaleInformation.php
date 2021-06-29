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
 * Class Frontend_Observer_SetLocaleInformation
 */
class Frontend_Observer_SetLocaleInformation extends Core_Observer implements Core_Interface_Observer
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
        /** @var Core_Page $page */
        $page = $object->getData('object');

        if ($page->getCustom('locale')) {
            setlocale(LC_ALL, $page->getCustom('locale'));
        }
    }
}
