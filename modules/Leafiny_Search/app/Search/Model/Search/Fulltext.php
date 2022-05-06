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
     * Words Table
     *
     * @var string $wordsTable
     */
    protected $wordsTable = 'search_words';

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
        $words = $this->getNearestWords($words, $language);

        $search = 'MATCH(`content`) AGAINST("' . $adapter->escape(join('* ', $words)) . '*" IN BOOLEAN MODE)';
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
        $columns = array_filter($columns);
        if (empty($columns)) {
            return false;
        }

        /** @var Core_Model $model */
        $model = App::getObject('model', $objectType);

        $columns = [
            'CONCAT(IFNULL(' . join(', ""), " ", IFNULL(', $columns) . ', "")) as content',
            '"' . $adapter->escape($objectType) . '" as object_type',
            '`main_table`.`' . $adapter->escape($model->getPrimaryKey()) . '` as object_id',
        ];
        if ($type['language'] ?? false) {
            $columns[] = '`main_table`.`' . $type['language'] . '` as language';
        }

        $select = $adapter->subQuery();
        if ($objectId) {
            $select->where('`main_table`.' . $model->getPrimaryKey(), $objectId);
        }
        $joins = array_filter($type['joins'] ?? []);
        foreach ($joins as $join) {
            $select->join($join['table'], $join['condition'], $join['type'] ?? 'LEFT');
        }
        $select->groupBy('`main_table`.`' . $adapter->escape($model->getPrimaryKey()) . '`');
        $select->get($model->getMainTable() . ' main_table', null, $columns);

        $update = ['content' => ''];
        if ($type['language'] ?? false) {
            $update['language'] = '';
        }

        $adapter->onDuplicate($update);
        $adapter->setInsertFromSelect($select);

        $result = (bool)$adapter->insert($this->getMainTable(), []);

        $this->refreshWords($objectType, $objectId);

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
     * Refresh the words. They are stored for suggestions or Levenshtein's algorithm.
     *
     * @param string   $objectType
     * @param int|null $objectId
     *
     * @return bool
     * @throws Exception
     */
    public function refreshWords(string $objectType, ?int $objectId = null): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }
        if (!$this->isEnabled($objectType)) {
            return false;
        }

        $type = $this->getObjectTypes()[$objectType] ?? false;

        $columns = $type['words'] ?? [];
        $columns = array_filter($columns);
        if (empty($columns)) {
            return false;
        }

        if ($type['language'] ?? false) {
            $columns[] = $type['language'];
        }

        /** @var Core_Model $model */
        $model = App::getObject('model', $objectType);

        $filters = [];
        if ($objectId) {
            $filters[] = [
                'column' => $model->getPrimaryKey(),
                'value'  => $objectId,
            ];
        }

        $objects = $model->getList($filters, [], null, [], $columns);

        $candidates = [];
        foreach ($objects as $object) {
            foreach ($columns as $column) {
                if (($type['language'] ?? false) === $column) {
                    continue;
                }
                $value = $object->getData($column);
                $words = explode(' ', preg_replace('/[^a-z ]+/i', '', strtolower(strip_tags($value))));
                foreach ($words as $word) {
                    if (strlen($word) <= 3) {
                        continue;
                    }
                    $candidates[$word] = [
                        'word'     => $word,
                        'language' => ($type['language'] ?? false) ? $object->getData($type['language']) : null,
                    ];
                }
            }
        }

        $adapter->setQueryOption(['IGNORE']);

        return (bool)$adapter->insertMulti($this->wordsTable, $candidates);
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
            $adapter->truncate($this->getMainTable());
            $adapter->truncate($this->wordsTable);
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
     * @param string      $query
     * @param string|null $language
     *
     * @return array
     */
    protected function getWords(string $query): array
    {
        $query = strtolower($query);
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

            $words[$key] = $word;
        }

        return $words;
    }

    /**
     * Retrieve the nearest words
     *
     * @param array       $words
     * @param string|null $language
     *
     * @return array
     * @throws Exception
     */
    protected function getNearestWords(array $words, ?string $language): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $words;
        }

        /** @var Search_Helper_Search $helper */
        $helper = App::getSingleton('helper', 'search');
        if (!$helper->canUseNearestWords()) {
            return $words;
        }

        if ($language) {
            $adapter->where('language', $language);
            $adapter->orWhere('language', NULL, 'IS');
        }
        $candidates = $adapter->get($this->wordsTable);

        foreach ($words as $word) {
            $max = strlen($word) - 2;
            $nearest = $word;
            foreach ($candidates as $candidate) {
                $distance = levenshtein($word, $candidate['word']);
                if ($distance > $max) {
                    continue;
                }
                $nearest = $candidate['word'];
                $max = $distance;
            }
            $words[] = $nearest;
        }

        return array_unique($words);
    }
}
