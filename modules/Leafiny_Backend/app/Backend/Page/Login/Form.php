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
 * Class Backend_Page_Login_Form
 */
class Backend_Page_Login_Form extends Backend_Page_Admin_Page_Abstract
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

        if ($user->isLoggedIn()) {
            $this->redirect($this->getUrl('/admin/*/'));
        }

        $loginPost = $this->getUrl($this->getBackendHelper()->getLoginPostPath());

        $this->setData('login_post', $loginPost);
    }

    /**
     * Need Login
     *
     * @return bool
     */
    public function needLogin(): bool
    {
        return false;
    }
}
