<?php

declare(strict_types=1);

/**
 * Class Backend_Model_Admin_User_Resources
 */
class Backend_Model_Admin_User_Resources extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'admin_user_resources';
    /**
     * Table primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'resource_id';

    /**
     * Update Admin User resources
     *
     * @param int $userId
     * @param array $resources
     *
     * @return bool
     * @throws Exception
     */
    public function update(int $userId, array $resources): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        if (empty($resources)) {
            return false;
        }

        $adapter->where('user_id', $userId);
        $adapter->delete($this->getMainTable());

        $data = [];

        foreach ($resources as $resource) {
            $data[] = [
                'user_id'  => $userId,
                'resource' => $resource
            ];
        }

        return $adapter->insertMulti($this->getMainTable(), $data) ? true : false;
    }

    /**
     * Check if user is allowed
     *
     * @param int    $userId
     * @param string $path
     *
     * @return bool
     * @throws Exception
     */
    public function isAllowed(int $userId, string $path): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('user_id', $userId);
        $adapter->where('resource', $path);

        $result = $adapter->getOne($this->getMainTable(), 1);

        return $result ? true : false;
    }

    /**
     * Retrieve all user allowed resources
     *
     * @param int $userId
     *
     * @return string[]
     * @throws Exception
     */
    public function getAllowedResources(int $userId): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('user_id', $userId);

        return array_column($adapter->get($this->getMainTable(), null, ['resource']), 'resource');
    }
}
