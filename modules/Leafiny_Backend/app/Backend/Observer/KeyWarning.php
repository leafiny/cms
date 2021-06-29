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
 * Class Backend_Observer_KeyWarning
 */
class Backend_Observer_KeyWarning extends Core_Observer implements Core_Interface_Observer
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
        /** @var Backend_Page_Admin_Page_Abstract $page */
        $page = $object->getData('object');

        if ($page->getObjectIdentifier() === '/admin/*/' && $page->getObjectKey() === 'leafiny') {
            $page->setWarningMessage(
                App::translate('Backend key is set to "leafiny". Please modify for more security.')
            );
        }
    }
}
