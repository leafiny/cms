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
 * Class Leafiny_MysqliDb
 */
class Leafiny_MysqliDb extends MysqliDb
{
    /**
     * Insert from select
     *
     * @var string|null $insertFromSelect
     */
    protected $insertFromSelect = null;
    /**
     * Prevent writing to database
     *
     * @var bool $noWriting
     */
    protected $noWriting = false;
    /**
     * Debug all queries
     *
     * @var bool $debug
     */
    protected $debug = false;
    /**
     * Save all queries
     *
     * @var Leafiny_Object[] $queries
     */
    protected $queries = [];

    /**
     * Method attempts to prepare the SQL query
     * and throws an error if there was a problem.
     *
     * @return mysqli_stmt
     * @throws Exception
     */
    protected function _prepareQuery()
    {
        if (!$this->noWriting) {
            if ($this->debug) {
                $this->queries[] = new Leafiny_Object(
                    ['number' => count($this->queries) + 1, 'query' => $this->_query, 'params' => $this->_bindParams]
                );
            }

            return parent::_prepareQuery();
        }

        if (preg_match('/^(INSERT|REPLACE|UPDATE|DELETE|ALTER|CREATE|DROP|RENAME|TRUNCATE)/i', $this->_query)) {
            $this->_query = 'SELECT 1';
            $this->_bindParams = [];
        }

        return parent::_prepareQuery();
    }

    /**
     * Abstraction method that will build an INSERT or UPDATE part of the query
     *
     * @param array $tableData
     *
     * @return void
     * @throws Exception
     */
    protected function _buildInsertQuery($tableData): void
    {
        if ($this->insertFromSelect !== null) {
            $this->_query .= $this->insertFromSelect;
            return;
        }

        parent::_buildInsertQuery($tableData);
    }

    /**
     * Abstraction method that will build the part of the WHERE conditions
     *
     * @param string $operator
     * @param array  $conditions
     *
     * @return void
     */
    protected function _buildCondition($operator, &$conditions): void
    {
        if (empty($conditions)) {
            return;
        }

        $this->_query .= ' ' . $operator;

        foreach ($conditions as $key => $cond) {
            list ($concat, $column, $operator, $val) = $cond;

            if (empty($concat)) {
                $concat = $concat . ' (';
            }
            if (strtolower($concat) === 'and') {
                $concat = ') ' . $concat . ' (';
            }
            
            switch (strtolower($operator)) {
                case 'not in':
                case 'in':
                    $this->_query .= ' ' . $concat . ' ' . $column;
                    $comparison = ' ' . $operator . ' (';
                    if (is_object($val)) {
                        $comparison .= $this->_buildPair('', $val);
                    } else {
                        foreach ($val as $v) {
                            $comparison .= ' ?,';
                            $this->_bindParam($v);
                        }
                    }
                    $this->_query .= rtrim($comparison, ',') . ' ) ';
                    break;
                case 'not between':
                case 'between':
                    $this->_query .= ' ' . $concat . ' ' . $column;
                    $this->_query .= ' $operator ? AND ? ';
                    $this->_bindParams($val);
                    break;
                case 'not exists':
                case 'exists':
                    $this->_query .= ' ' . $concat . ' ' . $column;
                    $this->_query .= $operator . $this->_buildPair('', $val);
                    break;
                case 'find_in_set':
                    $this->_query .= ' ' . $concat . ' ';
                    if (is_array($val)) {
                        $all = [];
                        foreach ($val as $value) {
                            $this->_bindParam($value);
                            $all[] = $operator . ' (? , ' . $column . ')';
                        }
                        $this->_query .= ' (' . join(' OR ', $all) . ')';
                    } else {
                        $this->_bindParam($val);
                        $this->_query .= $operator . ' (? , ' . $column . ')';
                    }
                    break;
                default:
                    $this->_query .= ' ' . $concat . ' ' . $column;
                    if (is_array($val)) {
                        $this->_bindParams($val);
                    } elseif ($val === null) {
                        $this->_query .= ' ' . $operator . ' NULL';
                    } elseif ($val != 'DBNULL' || $val == '0') {
                        $this->_query .= $this->_buildPair($operator, $val);
                    }
            }

            if (count($conditions) === ($key + 1)) {
                $this->_query .= ')';
            }
        }
    }

    /**
     * Helper function to add variables into the query statement
     *
     * @param array $tableData Variable with values
     *
     * @throws Exception
     */
    protected function _buildOnDuplicate($tableData)
    {
        if (is_array($this->_updateColumns) && !empty($this->_updateColumns)) {
            $this->_query .= " ON DUPLICATE KEY UPDATE ";
            if ($this->_lastInsertId) {
                $this->_query .= $this->_lastInsertId . "=LAST_INSERT_ID (" . $this->_lastInsertId . "), ";
            }

            foreach ($this->_updateColumns as $key => $val) {
                if (is_numeric($key)) {
                    $this->_updateColumns[$val] = '';
                    unset($this->_updateColumns[$key]);
                } else {
                    if ($val) {
                        $tableData[$key] = $val;
                    } else {
                        // Added: Update column with the new value
                        $this->_query .= $key . "=VALUES(". $key ."), ";
                        unset($this->_updateColumns[$key]);
                    }
                }
            }
            $this->_buildDataPairs($tableData, array_keys($this->_updateColumns), false);
        }
    }

    /**
     * Prevent writing to database
     *
     * @param bool $noWriting
     */
    public function setNoWriting(bool $noWriting): void
    {
        $this->noWriting = $noWriting;
    }

    /**
     * Enabled debug
     *
     * @param bool $debug
     */
    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * Retrieve all queries when debug is enabled
     *
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * Retrieve is no writing mode is enabled
     *
     * @return bool
     */
    public function isNoWriting(): bool
    {
        return $this->noWriting;
    }

    /**
     * Truncate a table
     *
     * @param string $table
     * @throws Exception
     */
    public function truncate(string $table): void
    {
        $this->query('TRUNCATE `' . $this->escape($table) . '`');
    }

    /**
     * CHeck if column exists in table
     *
     * @param string $table
     * @param string $column
     *
     * @return bool
     * @throws Exception
     */
    public function tableColumnExists(string $table, string $column): bool
    {
        $result = $this->mysqli()->query('SHOW COLUMNS FROM `' . $table . '` LIKE "' . $column . '"');

        if ($this->mysqli()->errno) {
            throw new Exception($this->mysqli()->error);
        }

        return (bool)$result->num_rows;
    }

    /**
     * CHeck if column exists in table
     *
     * @param string $table
     *
     * @return string[]
     * @throws Exception
     */
    public function getTableColumns(string $table): array
    {
        $columns = $this->mysqli()->query('SHOW COLUMNS FROM `' . $table . '`');

        if ($this->mysqli()->errno) {
            throw new Exception($this->mysqli()->error);
        }

        $result = [];

        foreach ($columns as $column) {
            $result[] = $column['Field'];
        }

        return $result;
    }

    /**
     * CHeck if column exists in table
     *
     * @param string $table
     *
     * @return string[]
     * @throws Exception
     */
    public function getNullableTableColumns(string $table): array
    {
        $columns = $this->mysqli()->query('SHOW COLUMNS FROM `' . $table . '`');

        if ($this->mysqli()->errno) {
            throw new Exception($this->mysqli()->error);
        }

        $result = [];

        foreach ($columns as $column) {
            if ($column['Null'] !== 'YES') {
                continue;
            }
            $result[] = $column['Field'];
        }

        return $result;
    }

    /**
     * Prepare Insert From Select
     *
     * @param MysqliDb $select
     *
     * @return void
     */
    public function setInsertFromSelect(MysqliDb $select): void
    {
        $insertFromSelect = '';
        $subQuery = $this->_buildPair("", $select);

        preg_match_all('/as (?<columns>.*?)[, ]/i', $subQuery, $matches);

        if (!empty($matches['columns'])) {
            $insertFromSelect .= ' (' . join(', ', $matches['columns']) . ') ';
        }

        $insertFromSelect .= ' ' . $subQuery . ' ';

        $this->insertFromSelect = $insertFromSelect;
    }

    /**
     * Reset states after an execution
     *
     * @return MysqliDb
     */
    protected function reset(): MysqliDb
    {
        $this->insertFromSelect = null;

        return parent::reset();
    }
}
