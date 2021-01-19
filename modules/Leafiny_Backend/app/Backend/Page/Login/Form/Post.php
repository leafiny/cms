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
 * Class Backend_Page_Login_Form_Post
 */
class Backend_Page_Login_Form_Post extends Backend_Page_Admin_Page_Abstract
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

        $post = $this->getPost();

        if (!$post->getData('username')) {
            $this->setErrorMessage(App::translate('Username is required'));
        }
        if (!$post->getData('password')) {
            $this->setErrorMessage(App::translate('Password is required'));
        }

        /** @var Backend_Model_Admin_User $model */
        $model = App::getObject('model', 'admin_user');

        $user = $model->getByUsername($post->getData('username'));

        if (!$user->hasData()) {
            $this->setErrorMessage(App::translate('Invalid username or password'));
        }

        if (!password_verify($post->getData('password'), $user->getData('password'))) {
            $this->setErrorMessage(App::translate('Invalid username or password'));
        }

        /** @var Backend_Model_Admin_User $model */
        $model = App::getObject('model', 'admin_user');

        $model->connect($user);

        $this->redirect('/admin/*/');
    }

    /**
     * Set error message and redirect
     *
     * @param string $message
     *
     * @return void
     */
    public function setErrorMessage(string $message): void
    {
        parent::setErrorMessage($message);

        $this->redirect($this->getBackendHelper()->getLoginPath());
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
