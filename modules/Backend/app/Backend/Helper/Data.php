<?php

declare(strict_types=1);

/**
 * Class Backend_Helper_Data
 */
class Backend_Helper_Data extends Core_Helper
{
    /**
     * Current user resources
     *
     * @var string[]|null
     */
    protected $userResources = null;

    /**
     * Check if resource is allowed for current admin user
     *
     * @param string $resource
     *
     * @return bool
     * @throws Exception
     */
    public function isAllowed(string $resource): bool
    {
        /** @var Backend_Model_Admin_User $user */
        $user = App::getSingleton('model', 'admin_user');

        if ($user->getCurrentUser()->getData('is_admin')) {
            return true;
        }

        if ($this->userResources === null) {
            /** @var Backend_Model_Admin_User_Resources $resources */
            $resources = App::getSingleton('model', 'admin_user_resources');

            $this->userResources = $resources->getAllowedResources($user->getCurrentUserId());
        }

        return in_array($resource, $this->userResources);
    }

    /**
     * Retrieve color from key
     *
     * @param int $key
     *
     * @return string
     */
    public function getColor(int $key): string
    {
        $colors = ['green', 'orange', 'blue', 'yellow', 'brown', 'purple', 'red'];

        if (is_array($this->getCustom('colors'))) {
            $colors = $this->getCustom('colors');
        }

        if ($key > count($colors)) {
            $key = $key - count($colors);
        }
        $key = $key - 1;

        $color = reset($colors);

        if (isset($colors[$key])) {
            $color = $colors[$key];
        }

        return $color;
    }

    /**
     * Retrieve Dashboard Path
     *
     * @return string
     */
    public function getDashboardPath(): string
    {
        return '/admin/*/';
    }

    /**
     * Retrieve Login Path
     *
     * @return string
     */
    public function getLoginPath(): string
    {
        return '/admin/*/login.html';
    }

    /**
     * Retrieve login post path
     *
     * @return string
     */
    public function getLoginPostPath(): string
    {
        return '/admin/*/login/post/';
    }

    /**
     * Retrieve login post path
     *
     * @return string
     */
    public function getUserAccountPath(): string
    {
        return '/admin/*/account/';
    }

    /**
     * Retrieve login post path
     *
     * @return string
     */
    public function getUserAccountSavePath(): string
    {
        return '/admin/*/account/save/';
    }

    /**
     * Retrieve login post path
     *
     * @return string
     */
    public function getUserLogoutPath(): string
    {
        return '/admin/*/logout/';
    }
}
