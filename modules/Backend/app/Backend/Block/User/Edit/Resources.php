<?php

declare(strict_types=1);

/**
 * Class Backend_Block_User_Edit_Resources
 */
class Backend_Block_User_Edit_Resources extends Core_Block
{
    /**
     * Contains a list of all admin pages
     *
     * @var string[]|null
     */
    protected $resources = null;

    /**
     * Retrieve all path list
     *
     * @return string[]
     */
    public function getResources(): array
    {
        if ($this->resources !== null) {
            return $this->resources;
        }

        /** @var string[] $identifiers */
        $identifiers = App::getConfig('page')[Leafiny_Config::TYPE_KEYS];

        $resources = [];

        foreach ($identifiers as $key => $identifier) {
            if (!preg_match('/^\/admin\/\*\//', $identifier)) {
                continue;
            }
            if (in_array($identifier, $this->getExcept())) {
                continue;
            }

            $parts = explode('/', trim($identifier, '/'));

            array_walk($parts, function (&$value) {
                $value = $this->translate(ucfirst($value));
            });

            if (empty($parts)) {
                continue;
            }

            $resources[$identifier] = join(' - ', array_slice($parts, 2));
        }

        asort($resources);

        $this->resources = $resources;

        return $this->resources;
    }

    /**
     * Retrieve column Element Number
     *
     * @return int
     */
    public function getColumnElementNumber(): int
    {
        return (int)ceil(count($this->getResources()) / 4);
    }

    /**
     * Retrieve user resources
     *
     * @param int|null $userId
     *
     * @return string[]
     * @throws Exception
     */
    public function getUserResources($userId): array
    {
        if (!$userId) {
            return array_keys($this->getResources());
        }

        /** @var Backend_Model_Admin_User $user */
        $user = App::getSingleton('model', 'admin_user');
        $current = $user->getById((int)$userId);

        if ($current->getData('is_admin')) {
            return array_keys($this->getResources());
        }

        /** @var Backend_Model_Admin_User_Resources $resources */
        $resources = App::getSingleton('model', 'admin_user_resources');

        return $resources->getAllowedResources((int)$current->getData('user_id'));
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
}
