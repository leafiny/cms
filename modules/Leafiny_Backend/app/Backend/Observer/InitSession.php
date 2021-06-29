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
 * Class Backend_Observer_InitSession
 */
class Backend_Observer_InitSession extends Core_Observer implements Core_Interface_Observer
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

        if (!$page->canProcess()) {
            return;
        }

        /** @var Backend_Session_Backend $session */
        $session = App::getSingleton('session', 'backend');
        $session->init();

        if (!$page->needLogin()) {
            return;
        }

        /** @var Backend_Model_Admin_User $user */
        $user = App::getSingleton('model', 'admin_user');

        if ($user->isLoggedIn()) {
            return;
        }

        App::getSession('backend')->destroy();

        $page->redirect($page->getUrl($page->getBackendHelper()->getLoginPath()));
    }
}
