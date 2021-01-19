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
 * Class Rewrite_Model_Rewrite
 */
class Rewrite_Model_Rewrite extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'url_rewrite';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'rewrite_id';

    /**
     * Retrieve Rewrite target from source
     *
     * @param string $source
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getBySource(string $source): Leafiny_Object
    {
        return parent::get($source, 'source_identifier');
    }

    /**
     * Refresh all configured rewrite
     *
     * @var string|null $rewriteType
     *
     * @return bool
     * @throws Exception
     */
    public function refreshAll(string $rewriteType = null): bool
    {
        /** @var array[] $refresh */
        $refresh = $this->getCustom('refresh');

        if (!is_array($refresh)) {
            return false;
        }

        if ($rewriteType !== null) {
            $refresh = array_intersect_key($refresh, array_flip([$rewriteType]));
        }

        foreach ($refresh as $type => $rewrite) {
            $object = new Leafiny_Object();
            $object->setData($rewrite);

            if (!$object->getData('enabled')) {
                continue;
            }

            $this->updateFromTable(
                $object->getData('table'),
                $object->getData('column'),
                $object->getData('source'),
                $object->getData('target'),
                $type
            );
        }

        $this->updatePages();

        return true;
    }

    /**
     * Check if rewrite exists
     *
     * @param string $source
     *
     * @return bool
     * @throws Exception
     */
    public function exists(string $source): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('source_identifier', $source);
        $result = $adapter->getOne($this->mainTable, 1);

        return $result ? true : false;
    }

    /**
     * Prevent page override with existing identifiers
     *
     * @return void
     * @throws Exception
     */
    public function updatePages(): void
    {
        $identifiers = App::getIdentifiers('page');

        $filter = [
            'column'   => 'source_identifier',
            'value'    => $identifiers,
            'operator' => 'IN'
        ];

        $rewrites = parent::getList([$filter]);

        foreach ($rewrites as $rewrite) {
            $object = new Leafiny_Object();
            $object->setData(
                [
                    'rewrite_id'        => $rewrite['rewrite_id'],
                    'source_identifier' => $rewrite['source_identifier'],
                    'target_identifier' => $rewrite['source_identifier'],
                ]
            );
            $this->save($object);
        }
    }

    /**
     * Update rewrite from table
     *
     * @param string $table
     * @param string $column
     * @param string $source
     * @param string $target
     * @param string $type
     *
     * @return bool
     * @throws Exception
     */
    public function updateFromTable(string $table, string $column, string $source, string $target, string $type): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $select = $adapter->subQuery();
        $select->groupBy($column);
        $select->get(
            $table,
            null,
            [
                'REPLACE("' . $source . '", "*", `' . $column . '`) as source_identifier',
                'REPLACE("' . $target . '", "*", `' . $column . '`) as target_identifier',
                '"' . $type . '" as object_type',
                '1 as is_system'
            ]
        );

        $adapter->setQueryOption(['IGNORE']);
        $adapter->setInsertFromSelect($select);
        $adapter->insert($this->getMainTable(), []);

        if ($adapter->getLastErrno()) {
            throw new Exception($adapter->getLastError());
        }

        return true;
    }

    /**
     * Object Validation
     *
     * @param Leafiny_Object $form
     *
     * @return string
     */
    public function validate(Leafiny_Object $form): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$form->getData('source_identifier')) {
            return 'Source cannot be empty';
        }
        if (!$form->getData('target_identifier')) {
            return 'Target cannot be empty';
        }

        return '';
    }
}
