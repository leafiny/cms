<?php

declare(strict_types=1);

/**
 * Class Backend_Page_Users_List_Action
 */
class Backend_Page_Users_List_Action extends Backend_Page_Admin_List_Action
{
    /**
     * Action Remove
     *
     * @param Leafiny_Object $post
     *
     * @return void
     */
    protected function remove(Leafiny_Object $post): void
    {
        if (!$post->getData('id')) {
            $this->setErrorMessage(App::translate('Please select items'));
            $this->redirect($this->getRefererUrl(), true);
        }

        /** @var Leafiny_Object $param */
        $param = $post->getData('id');

        try {
            /** @var Backend_Model_Admin_User $user */
            $user = App::getObject('model', 'admin_user');

            if ($user->size() === count($param->getData())) {
                $this->setErrorMessage(App::translate('You must keep at least one user'));
                $this->redirect($this->getRefererUrl(), true);
            }

            foreach ($param->getData() as $id) {
                if ((int)$id === $user->getCurrentUserId()) {
                    $this->setErrorMessage(App::translate('You can not delete your own account'));
                    $this->redirect($this->getRefererUrl(), true);
                }
            }
        } catch (Exception $exception) {
            $this->setErrorMessage($exception->getMessage());
            $this->redirect($this->getRefererUrl(), true);
        }

        parent::remove($post);
    }
}
