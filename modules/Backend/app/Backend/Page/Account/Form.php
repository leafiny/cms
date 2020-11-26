<?php

declare(strict_types=1);

/**
 * Class Backend_Page_Account_Form
 */
class Backend_Page_Account_Form extends Backend_Page_Admin_Form
{
    /**
     * Retrieve POST AND REQUEST params
     *
     * @return Leafiny_Object
     */
    public function getParams(): Leafiny_Object
    {
        $params = parent::getParams();

        /** @var Backend_Model_Admin_User $user */
        $user = App::getObject('model', 'admin_user');
        $params->setData('id', $user->getCurrentUserId());

        return $params;
    }
}
