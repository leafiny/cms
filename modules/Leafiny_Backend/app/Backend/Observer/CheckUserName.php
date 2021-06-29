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
 * Class Backend_Observer_CheckUserName
 */
class Backend_Observer_CheckUserName extends Core_Observer implements Core_Interface_Observer
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
        /** @var Backend_Model_Admin_User $user */
        $user = App::getSingleton('model', 'admin_user');

        if (!$page->needLogin()) {
            return;
        }

        if (in_array($page->getObjectIdentifier(), $this->getExcept())) {
            return;
        }

        if ($user->isAllowedUsername($user->getCurrentUser()->getData('username'))) {
            return;
        }

        $page->setWarningMessage(App::translate('Welcome! Please update user information before continuing'));
        $page->redirect($page->getUrl($this->getRedirectPath()));
    }

    /**
     * Retrieve excepted pages
     *
     * @return string[]
     */
    public function getExcept(): array
    {
        /** @var string[]|null $except */
        $except = $this->getCustom('except');

        if ($except === null) {
            /** @var Backend_Helper_Data $helper */
            $helper = App::getSingleton('helper', 'admin_data');

            $except = [
                $helper->getUserAccountPath(),
                $helper->getUserAccountSavePath(),
                $helper->getUserLogoutPath(),
                $helper->getLoginPath(),
                $helper->getLoginPostPath(),
            ];
        }

        return $except;
    }

    /**
     * Retrieve redirect path
     *
     * @return string
     */
    public function getRedirectPath(): string
    {
        /** @var string|null $except */
        $redirect = $this->getCustom('redirect_path');

        if ($redirect === null) {
            /** @var Backend_Helper_Data $helper */
            $helper = App::getSingleton('helper', 'admin_data');

            $redirect = $helper->getUserAccountPath();
        }

        return $redirect;
    }
}
