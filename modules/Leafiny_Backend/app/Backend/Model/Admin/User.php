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
 * Class Backend_Model_Admin_User
 */
class Backend_Model_Admin_User extends Core_Model
{
    /**
     * Admin user key in session
     *
     * @var string SESSION_ADMIN_USER_KEY
     */
    public const SESSION_ADMIN_USER_ID_KEY = 'admin_user_id';

    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'admin_user';
    /**
     * Table primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'user_id';
    /**
     * Current User
     *
     * @var Leafiny_Object|null $currentUser
     */
    protected $currentUser = null;

    /**
     * Save user date
     *
     * @param Leafiny_Object $user
     *
     * @return int|null
     * @throws Exception
     */
    public function save(Leafiny_Object $user): ?int
    {
        $password = $user->getData('new_password');

        if (!empty($password)) {
            if (!$this->isAllowedPassword($password)) {
                throw new Exception($this->getPasswordErrorMessage());
            }
            $user->setData('password', $this->encrypt($password));
        }

        $email = $user->getData('email');

        if ($email !== null) {
            $user->setData('email', strtolower($email));
        }

        return parent::save($user);
    }

    /**
     * Retrieve user by Username
     *
     * @param string $username
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getByUsername(string $username): Leafiny_Object
    {
        return $this->get($username, 'username');
    }

    /**
     * Retrieve user by id
     *
     * @param int $userId
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getById(int $userId): Leafiny_Object
    {
        return $this->get($userId);
    }

    /**
     * Admin User is logged In
     *
     * @return bool
     * @throws Exception
     */
    public function isLoggedIn(): bool
    {
        if (App::getSession('backend') === null) {
            return false;
        }

        return $this->getCurrentUser()->hasData();
    }

    /**
     * Retrieve current user id
     *
     * @return int
     */
    public function getCurrentUserId(): int
    {
        return (int)App::getSession('backend')->get(self::SESSION_ADMIN_USER_ID_KEY);
    }

    /**
     * Retrieve current user
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getCurrentUser(): Leafiny_Object
    {
        if (App::getSession('backend') === null) {
            return new Leafiny_Object();
        }

        if ($this->currentUser === null) {
            $this->currentUser = $this->getById($this->getCurrentUserId());
        }

        return $this->currentUser;
    }

    /**
     * Connect Admin User
     *
     * @param Leafiny_Object $user
     *
     * @return bool
     */
    public function connect(Leafiny_Object $user): bool
    {
        if (App::getSession('backend') === null) {
            return false;
        }

        if (!$user->hasData()) {
            return false;
        }

        $user->unsetData('password');

        App::getSession('backend')->regenerate();
        App::getSession('backend')->set(self::SESSION_ADMIN_USER_ID_KEY, $user->getData('user_id'));

        App::dispatchEvent('backend_user_connect', ['user' => $user]);

        return true;
    }

    /**
     * Disconnect admin user
     *
     * @return bool
     */
    public function disconnect(): bool
    {
        if (App::getSession('backend') === null) {
            return false;
        }

        $userId = App::getSession('backend')->get(self::SESSION_ADMIN_USER_ID_KEY);
        App::dispatchEvent('backend_user_disconnect', ['user_id' => $userId]);

        App::getSession('backend')->destroy();

        return true;
    }

    /**
     * User validation
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

        if (!$object->getData('firstname')) {
            return 'The first name cannot be empty';
        }
        if (!$object->getData('lastname')) {
            return 'The last name cannot be empty';
        }
        if (!$object->getData('email')) {
            return 'The Email cannot be empty';
        }
        if (!$object->getData('username')) {
            return 'The username cannot be empty';
        }

        if (!$this->isAllowedEmail($object->getData('email'))) {
            return 'Email is not valid';
        }
        if (!$this->isAllowedUsername($object->getData('username'))) {
            return 'This username is not allowed';
        }

        if ($object->getData('new_password')) {
            if (!$this->isAllowedPassword($object->getData('new_password'))) {
                return $this->getPasswordErrorMessage();
            }
        }

        return '';
    }

    /**
     * Retrieve not allowed usernames
     *
     * @return array
     */
    public function getNotAllowedUsername(): array
    {
        if ($this->getCustom('not_allowed_username') !== null) {
            return $this->getCustom('not_allowed_username');
        }

        return ['admin', 'root', 'test', 'user', 'guest', 'student', 'administrator', 'demo'];
    }

    /**
     * Is allowed Username
     *
     * @param string $username
     *
     * @return bool
     */
    public function isAllowedUsername(string $username): bool
    {
        return !in_array($username, $this->getNotAllowedUsername());
    }

    /**
     * Retrieve password error message
     *
     * @return string
     */
    public function getPasswordErrorMessage(): string
    {
        if ($this->getCustom('password_error_message')) {
            return (string)$this->getCustom('password_error_message');
        }

        return 'Password must contain at least 8 characters, one letter and one number';
    }

    /**
     * Retrieve password regex validator
     *
     * @return string
     */
    public function getPasswordRegex(): string
    {
        if ($this->getCustom('password_regex')) {
            return (string)$this->getCustom('password_regex');
        }

        return '/^(?=.*\d)(?=.*[a-zA-Z]).{8,}$/';
    }

    /**
     * Is allowed Password
     *
     * @param string $password
     *
     * @return bool
     */
    public function isAllowedPassword(string $password): bool
    {
        return (bool)preg_match($this->getPasswordRegex(), $password);
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

    /**
     * Encrypt value
     *
     * @param string $value
     *
     * @return string
     */
    public function encrypt(string $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }
}
