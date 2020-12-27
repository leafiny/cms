<?php

declare(strict_types=1);

/**
 * Class Backend_Block_Account
 */
class Backend_Block_Account extends Core_Block
{
    /**
     * Current user
     *
     * @var null|Leafiny_Object $user
     */
    protected $user = null;

    /**
     * Retrieve current user id
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getUser(): Leafiny_Object
    {
        if ($this->user === null) {
            /** @var Backend_Model_Admin_User $user */
            $user = App::getSingleton('model', 'admin_user');
            $this->user = $user->getCurrentUser();
        }

        return $this->user;
    }

    /**
     * Retrieve user full name
     *
     * @return string
     * @throws Exception
     */
    public function getUserFullName(): string
    {
        $user = $this->getUser();

        return $user->getData('firstname') . ' ' . $user->getData('lastname');
    }

    /**
     * Retrieve user account URL
     *
     * @return string
     */
    public function getAccountUrl(): string
    {
        /** @var Backend_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'admin_data');

        return $this->getUrl($helper->getUserAccountPath());
    }

    /**
     * Retrieve user logout URL
     *
     * @return string
     */
    public function getLogoutUrl(): string
    {
        /** @var Backend_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'admin_data');

        return $this->getUrl($helper->getUserLogoutPath());
    }
}
