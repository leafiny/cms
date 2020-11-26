<?php

declare(strict_types=1);

/**
 * Class Backend_Page_Account_Logout
 */
class Backend_Page_Account_Logout extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        /** @var Backend_Model_Admin_User $user */
        $user = App::getObject('model', 'admin_user');
        $user->disconnect();

        $this->redirect($this->getBackendHelper()->getLoginPath());
    }
}
