<?php

declare(strict_types=1);

/**
 * Class Leafiny_Session
 */
class Leafiny_Session
{
    /**
     * Prefix for sessions
     *
     * @var string
     */
    private static $prefix = 'sess_';

    /**
     * Determine if session has started
     *
     * @var bool
     */
    private static $sessionStarted = false;

    /**
     * Set prefix for sessions
     *
     * @param string $prefix
     *
     * @return bool
     */
    public static function setPrefix(string $prefix): bool
    {
        return is_string(self::$prefix = $prefix);
    }

    /**
     * Get prefix for sessions
     *
     * @return string
     */
    public static function getPrefix(): string
    {
        return self::$prefix;
    }

    /**
     * If session has not started, start sessions
     *
     * @param int    $lifeTime
     * @param string $sameSite
     * @param string $domain
     * @param bool   $secure
     * @param string $name
     * @param bool   $httpOnly
     *
     * @return bool
     */
    public static function init(
        int $lifeTime = 0,
        string $sameSite = 'Strict',
        string $domain = '',
        bool $secure = false,
        string $name = '',
        bool $httpOnly = true
    ): bool {
        if (self::$sessionStarted === false) {
            if (!empty($name)) {
                session_name($name);
            }
            session_set_cookie_params($lifeTime, '/; samesite=' . $sameSite, $domain, $secure, $httpOnly);
            session_start();

            return self::$sessionStarted = true;
        }

        return false;
    }

    /**
     * Add value to a session
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return bool
     */
    public static function set($key, $value = false): bool
    {
        if (is_array($key) && $value === false) {
            foreach ($key as $name => $value) {
                $_SESSION[self::$prefix . $name] = $value;
            }
        } else {
            $_SESSION[self::$prefix . $key] = $value;
        }

        return true;
    }

    /**
     * Extract session item, delete session item and finally return the item.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public static function pull(string $key)
    {
        if (isset($_SESSION[self::$prefix . $key])) {
            $value = $_SESSION[self::$prefix . $key];
            unset($_SESSION[self::$prefix . $key]);

            return $value;
        }

        return null;
    }

    /**
     * Get item from session.
     *
     * @param string      $key
     * @param string|null $secondKey
     *
     * @return mixed|null
     */
    public static function get(string $key = '', ?string $secondKey = null)
    {
        $name = self::$prefix . $key;

        if (empty($key)) {
            return isset($_SESSION) ? $_SESSION : null;
        }

        if ($secondKey !== null) {
            if (isset($_SESSION[$name][$secondKey])) {
                return $_SESSION[$name][$secondKey];
            }
        }

        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * Check item from session.
     *
     * @param string      $key
     * @param string|null $secondKey
     *
     * @return bool
     */
    public static function has(string $key = '', ?string $secondKey = null): bool
    {
        $name = self::$prefix . $key;

        if (empty($key)) {
            return isset($_SESSION);
        }

        if ($secondKey !== null) {
            return isset($_SESSION[$name][$secondKey]);
        }

        return isset($_SESSION[$name]);
    }

    /**
     * Get session id.
     *
     * @return string
     */
    public static function id(): string
    {
        return session_id();
    }

    /**
     * Regenerate session_id.
     *
     * @return string
     */
    public static function regenerate(): string
    {
        session_regenerate_id(true);

        return session_id();
    }

    /**
     * Empties and destroys the session.
     *
     * @param string $key
     * @param bool   $prefix
     *
     * @return bool
     */
    public static function destroy(string $key = '', bool $prefix = false): bool
    {
        if (self::$sessionStarted === true) {
            if ($key === '' && $prefix === false) {
                session_unset();
                session_destroy();
            } elseif ($prefix === true) {
                foreach ($_SESSION as $index => $value) {
                    if (strpos($index, self::$prefix) === 0) {
                        unset($_SESSION[$index]);
                    }
                }
            } else {
                unset($_SESSION[self::$prefix . $key]);
            }

            return true;
        }

        return false;
    }
}
