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
 * Class Search_Model_Search_Fulltext
 */
class Search_Model_Search_Fulltext extends Core_Model implements Search_Interface_Engine
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'search_fulltext';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'search_id';

    /**
     * Search in fulltext table
     *
     * @param string $query
     * @param string $objectType
     * @param string|null $language
     *
     * @return int[]
     * @throws Exception
     */
    public function search(string $query, string $objectType, ?string $language = null): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }
        if (!$query) {
            return [];
        }
        if (!$this->isEnabled($objectType)) {
            return [];
        }

        $queryData = new Leafiny_Object(
            [
                'query'       => $query,
                'object_type' => $objectType,
                'language'    => $language,
            ]
        );

        App::dispatchEvent('search_fulltext_search_before', ['query' => $queryData]);

        $words = $this->getWords($queryData->getData('query'));

        $search = 'MATCH(`content`) AGAINST("' . $adapter->escape(join(' ', $words)) . '" IN BOOLEAN MODE)';
        $where = [
            '`object_type` = "' . $adapter->escape($queryData->getData('object_type')) . '"',
            $search
        ];
        if ($queryData->getData('language')) {
            $where[] = '(`language` = "' . $queryData->getData('language') . '" OR `language` IS NULL)';
        }

        $rows = $adapter->rawQuery('
            SELECT `object_id` FROM `' . $this->getMainTable() . '`
            WHERE ' . join(' AND ', $where) . '
            ORDER BY ' . $search . ' DESC
        ');

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        $result = [];
        foreach ($rows as $item) {
            $result[] = (int)$item['object_id'];
        }

        $queryData->setData('result', $result);

        App::dispatchEvent('search_fulltext_search_after', ['query' => $queryData]);

        return $queryData->getData('result');
    }

    /**
     * Refresh data by object type
     *
     * @param string   $objectType
     * @param int|null $objectId
     *
     * @return bool
     * @throws Exception
     */
    public function refresh(string $objectType, ?int $objectId = null): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        if (!$this->isEnabled($objectType)) {
            return false;
        }

        $type = $this->getObjectTypes()[$objectType] ?? false;

        $columns = $type['columns'] ?? [];
        if (empty($columns)) {
            return false;
        }
        foreach ($columns as $key => $column) {
            $columns[$key] = $adapter->escape($column);
        }

        /** @var Core_Model $model */
        $model = App::getObject('model', $objectType);

        $columns = [
            'CONCAT(IFNULL(`' . join('`, ""), " ", IFNULL(`', $columns) . '`, "")) as content',
            '"' . $adapter->escape($objectType) . '" as object_type',
            '`' . $adapter->escape($model->getPrimaryKey()) . '` as object_id',
        ];
        if ($type['language'] ?? false) {
            $columns[] = '`' . $type['language'] . '` as language';
        }

        $select = $adapter->subQuery();
        if ($objectId) {
            $select->where($model->getPrimaryKey(), $objectId);
        }
        $select->get($model->getMainTable(), null, $columns);

        $update = ['content' => ''];
        if ($type['language'] ?? false) {
            $update['language'] = '';
        }

        $adapter->onDuplicate($update);
        $adapter->setInsertFromSelect($select);

        $result = (bool)$adapter->insert($this->getMainTable(), []);

        App::dispatchEvent(
            'search_fulltext_refresh_after',
            [
                'object_type' => $objectType,
                'object_id'   => $objectId
            ]
        );

        return $result;
    }

    /**
     * Delete data by object type
     *
     * @param string   $objectType
     * @param int|null $objectId
     *
     * @return bool
     * @throws Exception
     */
    public function remove(string $objectType, ?int $objectId = null): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        if (!$this->isEnabled($objectType)) {
            return false;
        }

        $adapter->where('object_type', $objectType);
        if ($objectId) {
            $adapter->where('object_id', $objectId);
        }

        $result = $adapter->delete($this->getMainTable());

        App::dispatchEvent(
            'search_fulltext_remove_after',
            [
                'object_type' => $objectType,
                'object_id'   => $objectId
            ]
        );

        return $result;
    }

    /**
     * Refresh data for all object types
     *
     * @return void
     * @throws Exception
     */
    public function refreshAll(): void
    {
        $adapter = $this->getAdapter();
        if ($adapter) {
            foreach ($this->getObjectTypes() as $type => $options) {
                $this->refresh($type);
            }
        }
    }

    /**
     * Retrieve all object types with options
     *
     * @return mixed[]
     */
    public function getObjectTypes(): array
    {
        /** @var Search_Helper_Search $helper */
        $helper = App::getSingleton('helper', 'search');

        return $helper->getObjectTypes();
    }

    /**
     * Check if object type is enabled
     *
     * @param string $objectType
     * @return bool
     */
    public function isEnabled(string $objectType): bool
    {
        $type = $this->getObjectTypes()[$objectType] ?? false;
        if (!$type) {
            return false;
        }
        if (!($type['enabled'] ?? false)) {
            return false;
        }

        return true;
    }

    /**
     * Extract words to search
     *
     * @param string $query
     *
     * @return array
     */
    protected function getWords(string $query): array
    {
        $query = str_replace(['+', '*'], ' ', $query);
        $words = explode(' ', $query);

        foreach ($words as $key => $word) {
            $word = str_replace(['+', '-', '@', '<', '>', '(', ')', '~', '*', '"'], '', $word);
            if (!$word) {
                unset($words[$key]);
                continue;
            }
            if (substr($word, -1) === 's') {
                $word = substr($word, 0, -1);
            }
            if (strlen($word) < 2) {
                unset($words[$key]);
                continue;
            }

            $words[$key] = $word . '*';
        }

        return $words;
    }
}
