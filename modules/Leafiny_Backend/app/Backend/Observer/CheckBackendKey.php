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
 * Class Backend_Observer_CheckBackendKey
 */
class Backend_Observer_CheckBackendKey extends Core_Observer implements Core_Interface_Observer
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
        /** @var Core_Page $page */
        $page = $object->getData('object');

        if ($page->getContext() !== Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND) {
            return;
        }

        if ($page->getObjectKey() === App::getConfig('app.backend_key')) {
            return;
        }

        $page->setContext(Backend_Page_Admin_Page_Abstract::CONTEXT_DEFAULT);
        $page->error(true);
    }
}
