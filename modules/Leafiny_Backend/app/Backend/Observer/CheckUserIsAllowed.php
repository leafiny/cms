<?php

declare(strict_types=1);

/**
 * Class Backend_Observer_CheckUserIsAllowed
 */
class Backend_Observer_CheckUserIsAllowed extends Core_Event
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

        $current = $user->getCurrentUser();

        if ($current->getData('is_admin')) {
            return;
        }

        if (in_array($page->getObjectIdentifier(), $this->getExcept())) {
            return;
        }

        /** @var Backend_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'admin_data');

        if ($helper->isAllowed($page->getObjectIdentifier())) {
            return;
        }

        $page->setErrorMessage(App::translate('This action is not allowed.'));
        $page->redirect($this->getRedirectPath());
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
                $helper->getDashboardPath(),
                $helper->getLoginPath(),
                $helper->getLoginPostPath(),
                $helper->getUserLogoutPath()
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

            $redirect = $helper->getDashboardPath();
        }

        return $redirect;
    }
}
