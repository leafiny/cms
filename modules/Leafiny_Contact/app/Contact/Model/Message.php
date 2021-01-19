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
 * Class Contact_Model_Message
 */
class Contact_Model_Message extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'contact_message';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'message_id';

    /**
     * Message validation
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

        if (!$form->getData('name')) {
            return 'The name cannot be empty';
        }
        if (!$form->getData('email')) {
            return 'The email cannot be empty';
        }
        if (!$form->getData('message')) {
            return 'The message cannot be empty';
        }
        if (!$this->isAllowedEmail($form->getData('email'))) {
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
