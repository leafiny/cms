<?php

declare(strict_types=1);

/**
 * Class Backend_Page_Account_Form_Save
 */
class Backend_Page_Account_Form_Save extends Backend_Page_Admin_Form_Save
{
    /**
     * Retrieve only POST params
     *
     * @return Leafiny_Object
     */
    public function getPost(): Leafiny_Object
    {
        $post = parent::getPost();

        /** @var Backend_Model_Admin_User $user */
        $user = App::getObject('model', 'admin_user');
        $post->setData('user_id', $user->getCurrentUserId());

        return $post;
    }
}
