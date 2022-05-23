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
 * Class Core_Model
 */
class Core_Model extends Leafiny_Object
{
    /**
     * MysqliDb
     *
     * @var Leafiny_MysqliDb[] $resource
     */
    private static $resource = [];
    /**
     * Main Table
     *
     * @var string|null $mainTable
     */
    protected $mainTable = null;
    /**
     * Table primary key
     *
     * @var string|null $primaryKey
     */
    protected $primaryKey = null;

    /**
     * Retrieve Adapter
     *
     * @return Leafiny_MysqliDb|null
     * @throws Exception
     */
    public function getAdapter(): ?Leafiny_MysqliDb
    {
        if (!$this->getDbHost()) {
            return null;
        }

        $key = md5($this->getDbHost() . $this->getDbDatabase() . $this->getDbPort());

        if (!isset(self::$resource[$key])) {
            self::$resource[$key] = new Leafiny_MysqliDb(
                $this->getDbHost(),
                $this->getDbUsername(),
                $this->getDbPassword(),
                $this->getDbDatabase(),
                $this->getDbPort() ? $this->getDbPort() : null
            );
            if ($this->isDbDebug()) {
                self::$resource[$key]->setDebug(true);
            }
            if ($this->getDbLcTimeNames()) {
                self::$resource[$key]->rawQuery('SET lc_time_names = ?', [$this->getDbLcTimeNames()]);
            }
            if ($this->isDbNoWriting()) {
                self::$resource[$key]->setNoWriting(true);
            }
        }

        return self::$resource[$key];
    }

    /**
     * Retrieve all data
     *
     * @param array $filters
     * @param array $orders
     * @param array|null $limit
     * @param array $joins
     * @param array $columns
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getList(array $filters = [], array $orders = [], ?array $limit = null, array $joins = [], array $columns = []): array
    {
        $result = [];

        if ($this->getMainTable() === null) {
            return $result;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $result;
        }

        foreach ($filters as $filter) {
            if (!isset($filter['column'])) {
                continue;
            }
            if (!$adapter->tableColumnExists($this->getMainTable(), $filter['column'])) {
                continue;
            }
            if (!isset($filter['operator'])) {
                $filter['operator'] = '=';
            }

            $condition = $filter['condition'] ?? 'and';

            if (strtolower($condition) === 'and') {
                $adapter->where('main_table.' . $filter['column'], $filter['value'], $filter['operator']);
            }
            if (strtolower($condition) === 'or') {
                $adapter->orWhere('main_table.' . $filter['column'], $filter['value'], $filter['operator']);
            }
        }

        foreach ($orders as $order) {
            if (!isset($order['order'], $order['dir'])) {
                continue;
            }
            if (!$adapter->tableColumnExists($this->getMainTable(), $order['order'])) {
                continue;
            }

            $adapter->orderBy('main_table.' . $order['order'], $order['dir'], $order['custom'] ?? null);
        }

        foreach ($joins as $join) {
            if (!isset($join['table'], $join['condition'])) {
                continue;
            }
            $adapter->join($join['table'], $join['condition']);
        }

        if (empty($columns)) {
            $columns = 'main_table.*';
        }

        $result = $adapter->get($this->getMainTable() . ' as main_table', $limit, $columns);

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        foreach ($result as $key => $block) {
            $object = new Leafiny_Object();
            $object->setData($block);

            $result[$key] = $object;
        }

        App::dispatchEvent($this->getObjectIdentifier() . '_get_list_after', ['result' => $result]);

        return $result;
    }

    /**
     * Retrieve table size
     *
     * @param array $filters
     *
     * @return int
     * @throws Exception
     */
    public function size(array $filters = []): int
    {
        $size = 0;

        if ($this->getMainTable() === null) {
            return $size;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $size;
        }

        foreach ($filters as $filter) {
            if (!isset($filter['column'])) {
                continue;
            }
            if (!$adapter->tableColumnExists($this->getMainTable(), $filter['column'])) {
                continue;
            }
            if (!isset($filter['operator'])) {
                $filter['operator'] = '=';
            }

            $adapter->where($filter['column'], $filter['value'], $filter['operator']);
        }

        $size = $adapter->getValue($this->getMainTable(), 'COUNT(*)');

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        return (int)$size;
    }

    /**
     * Retrieve data
     *
     * @param mixed $value
     * @param string|null $column
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function get($value, ?string $column = null): Leafiny_Object
    {
        $object = new Leafiny_Object();

        if ($this->getMainTable() === null) {
            return $object;
        }

        if (!$column) {
            $column = $this->getPrimaryKey();
        }
        if (!$column) {
            return $object;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $object;
        }

        $adapter->where('main_table.' . $column, $value);
        $result = $adapter->getOne($this->getMainTable() . ' as main_table');

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        if ($result) {
            $object->setData($result);
        }

        App::dispatchEvent($this->getObjectIdentifier() . '_get_after', ['object' => $object]);

        return $object;
    }

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        if ($this->getMainTable() === null) {
            return false;
        }
        if ($this->getPrimaryKey() === null) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        App::dispatchEvent(
            'object_delete_before',
            [
                'identifier' => $this->getObjectIdentifier(),
                'object_id'  => $id
            ]
        );
        App::dispatchEvent($this->getObjectIdentifier() . '_delete_before', ['object_id' => $id]);

        $adapter->where($this->getPrimaryKey(), $id);
        $result = $adapter->delete($this->getMainTable());

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        if ($result) {
            App::dispatchEvent(
                'object_delete_after',
                [
                    'identifier' => $this->getObjectIdentifier(),
                    'object_id'  => $id
                ]
            );
            App::dispatchEvent($this->getObjectIdentifier() . '_delete_after', ['object_id' => $id]);
        }

        return $result;
    }

    /**
     * Save
     *
     * @param Leafiny_Object $object
     *
     * @return int|null
     * @throws Exception
     */
    public function save(Leafiny_Object $object): ?int
    {
        if ($this->getMainTable() === null) {
            return null;
        }
        if ($this->getPrimaryKey() === null) {
            return null;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return null;
        }

        App::dispatchEvent(
            'object_save_before',
            [
                'object'     => $object,
                'object_id'  => $object->getData($this->getPrimaryKey()),
                'identifier' => $this->getObjectIdentifier(),
            ]
        );
        App::dispatchEvent($this->getObjectIdentifier() . '_save_before', ['object' => $object]);

        $validation = $this->validate($object);
        if (!empty($validation)) {
            throw new Exception($validation);
        }

        $data     = $object->getData();
        $origin   = $data;
        $columns  = $this->getAdapter()->getTableColumns($this->getMainTable());
        $nullable = $this->getAdapter()->getNullableTableColumns($this->getMainTable());

        foreach ($data as $column => $value) {
            if (!in_array($column, $columns)) {
                unset($data[$column]);
            }
            if ($value === '' && in_array($column, $nullable)) {
                $data[$column] = null;
            }
        }
        $object->setData($data);

        if ($object->getData($this->getPrimaryKey())) {
            $adapter->where($this->getPrimaryKey(), $object->getData($this->getPrimaryKey()));
            $adapter->update($this->mainTable, $object->getData());
        } else {
            $object->setData($this->getPrimaryKey(), null);
            $objectId = $adapter->insert($this->mainTable, $object->getData());
            $origin[$this->getPrimaryKey()] = $objectId;
        }

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        $object->addData($origin);

        App::dispatchEvent(
            'object_save_after',
            [
                'object'     => $object,
                'object_id'  => $object->getData($this->getPrimaryKey()),
                'identifier' => $this->getObjectIdentifier(),
            ]
        );
        App::dispatchEvent($this->getObjectIdentifier() . '_save_after', ['object' => $object]);

        return (int)$object->getData($this->getPrimaryKey());
    }

    /**
     * Model validation
     *
     * @param Leafiny_Object $object
     *
     * @return string
     */
    public function validate(Leafiny_Object $object): string
    {
        return '';
    }

    /**
     * Retrieve table primary key
     *
     * @return string|null
     */
    public function getPrimaryKey(): ?string
    {
        return $this->primaryKey;
    }

    /**
     * Retrieve main table
     *
     * @return string|null
     */
    public function getMainTable(): ?string
    {
        return $this->mainTable;
    }

    /**
     * Retrieve object identifier
     *
     * @return string|null
     */
    public function getObjectIdentifier(): ?string
    {
        return $this->getData('object_identifier');
    }

    /**
     * Retrieve Database Host
     *
     * @return string|null
     */
    public function getDbHost(): ?string
    {
        return $this->getCustom('db_host');
    }

    /**
     * Retrieve Database username
     *
     * @return string|null
     */
    public function getDbUsername(): ?string
    {
        return $this->getCustom('db_username');
    }

    /**
     * Retrieve Database password
     *
     * @return string|null
     */
    public function getDbPassword(): ?string
    {
        return $this->getCustom('db_password');
    }

    /**
     * Retrieve Database name
     *
     * @return string|null
     */
    public function getDbDatabase(): ?string
    {
        return $this->getCustom('db_database');
    }

    /**
     * Retrieve Database port
     *
     * @return int|null
     */
    public function getDbPort(): ?int
    {
        return $this->getCustom('db_port');
    }

    /**
     * Retrieve Database lc time names
     *
     * @return string|null
     */
    public function getDbLcTimeNames(): ?string
    {
        return $this->getCustom('lc_time_names');
    }

    /**
     * Retrieve if writing in database must be disabled
     *
     * @return bool
     */
    public function isDbNoWriting(): bool
    {
        return (bool)$this->getCustom('db_no_writing');
    }

    /**
     * Retrieve if debug is enabled
     *
     * @return bool
     */
    public function isDbDebug(): bool
    {
        return (bool)$this->getCustom('db_debug');
    }

    /**
     * Get custom data
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getCustom(string $key)
    {
        /** @var Leafiny_Object $custom */
        $custom = $this->getData(Core_Object_Factory::CUSTOM_KEY);

        if (!$custom) {
            return null;
        }

        return $custom->getData($key);
    }

    /**
     * Set custom data
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Core_Model
     */
    public function setCustom(string $key, $value): Core_Model
    {
        /** @var Leafiny_Object $custom */
        $custom = $this->getData(Core_Object_Factory::CUSTOM_KEY);

        $custom->setData($key, $value);

        return $this;
    }
}
