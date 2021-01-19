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
 * Class Redirect_Model_Redirect
 */
class Redirect_Model_Redirect extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'url_redirect';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'redirect_id';

    /**
     * Retrieve Redirect target from source
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
     * Check if redirect exists
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
