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
 * Class Social_Model_Comment
 */
class Social_Model_Comment extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'social_comment';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'comment_id';

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
        if (!$object->getData('language')) {
            $object->setData('language', App::getLanguage());
        }
        if (!$object->getData('ip_address')) {
            $object->setData('ip_address', $this->getClientIp());
        }
        if ($object->getData('status') === null) {
            $object->setData('status', $this->getDefaultStatus());
        }
        if ($object->getData('comment')) {
            $object->setData(
                'comment',
                preg_replace('#\R+#', "\n\n", strip_tags($object->getData('comment')))
            );
        }

        return parent::save($object);
    }

    /**
     * Retrieve entity comments
     *
     * @param int    $entityId
     * @param string $entityType
     * @param string $language
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getEntityComments(int $entityId, string $entityType, string $language): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('entity_id', $entityId);
        $adapter->where('entity_type', $entityType);
        $adapter->where('status', 1);
        $adapter->where('language', $language);

        $adapter->orderBy('created_at', 'DESC');

        $result = $adapter->get($this->getMainTable());

        foreach ($result as $key => $comment) {
            $result[$key] = new Leafiny_Object($comment);
        }

        return $result;
    }

    /**
     * Retrieve default status
     *
     * @return int
     */
    public function getDefaultStatus(): int
    {
        return (int)$this->getCustom('default_status');
    }

    /**
     * Retrieve client IP address
     *
     * @return string
     */
    protected function getClientIp(): string
    {
        return (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'undefined';
    }

    /**
     * Message validation
     *
     * @param Leafiny_Object $object
     *
     * @return string
     */
    public function validate(Leafiny_Object $object): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$object->getData('entity_id')) {
            return 'The entity identifier cannot be empty';
        }
        if (!$object->getData('entity_type')) {
            return 'The entity type cannot be empty';
        }
        if (!$object->getData('comment')) {
            return 'The comment cannot be empty';
        }
        if ($object->getData('email') && !$this->isAllowedEmail($object->getData('email'))) {
            return 'Email is not valid';
        }

        return '';
    }

    /**
     * Is allowed Email
     *
     * @param string $email
     *
     * @return bool
     */
    public function isAllowedEmail(string $email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
