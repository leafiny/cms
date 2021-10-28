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

define('DS', DIRECTORY_SEPARATOR);
define('US', '/');

/**
 * Class App
 */
final class App
{
    /**
     * @var string VERSION
     */
    public const VERSION = '1.9.2';
    /**
     * @var string MODULES_DIRECTORY
     */
    public const MODULES_DIRECTORY = 'modules';
    /**
     * @var string FRONTEND_MODULE_NAME
     */
    public const FRONTEND_MODULE_NAME = 'Frontend';
    /**
     * @var string BACKEND_MODULE_NAME
     */
    public const BACKEND_MODULE_NAME = 'Backend';
    /**
     * @var string CLASS_SYMLINKS_DIRECTORY
     */
    public const CLASS_SYMLINKS_DIRECTORY = 'load';

    /**
     * Contains Objects
     *
     * @var Leafiny_Object[] $singleton
     */
    protected static $singleton = [];
    /**
     * Object Factory
     *
     * @var Core_Object_Factory $factory
     */
    protected static $factory = null;
    /**
     * Contains Config Object
     *
     * @var Core_Config $config
     */
    protected static $config = null;
    /**
     * Application HTTP Protocol (http or https)
     *
     * @var string $protocol
     */
    protected static $protocol = null;
    /**
     * Application Root Directory
     *
     * @var string $rootDir
     */
    protected static $rootDir = null;
    /**
     * Current page identifier
     *
     * @var string $identifier
     */
    protected static $identifier = null;
    /**
     * Current application environment (dev, preprod, default)
     *
     * @var string $environment
     */
    protected static $environment = null;
    /**
     * Current application language (en_US, fr_FR, de_DE...)
     *
     * @var string $language
     */
    protected static $language = null;
    /**
     * Contains modules list name
     *
     * @var string[] $modules
     */
    protected static $modules = null;

    /**
     * Run application
     *
     * @return void
     * @throws Throwable
     */
    public static function run(): void
    {
        try {
            $autoload = self::getRootDir() . 'vendor' . DS . 'autoload.php';
            if (is_file($autoload)) {
                require $autoload;
            }
            self::processMaintenance();
            self::requirements();
            self::processSetup();
            self::setIdentifier(self::getRequestUri());

            echo self::getPage(self::getIdentifier());
        } catch (Throwable $throwable) {
            self::processError($throwable);
        }
    }

    /**
     * Retrieve current version
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Retrieve Session
     *
     * @param string $identifier
     *
     * @return Leafiny_Session|null
     */
    public static function getSession(string $identifier = ''): ?Leafiny_Session
    {
        /** @var Core_Session $session */
        $session = self::getSingleton('session', $identifier ?: null);

        return $session->getSession();
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     *
     * @return void
     */
    public static function setIdentifier(string $identifier): void
    {
        self::$identifier = $identifier;
    }

    /**
     * Retrieve identifier
     *
     * @çeturn string
     */
    public static function getIdentifier(): string
    {
        return (string)self::$identifier;
    }

    /**
     * Retrieve page
     *
     * @param string $requestUri
     *
     * @return string
     * @throws Throwable
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    public static function getPage(string $requestUri): string
    {
        /** @var Core_Page $object */
        $object = self::getObject('page', $requestUri);

        return $object->render();
    }

    /**
     * Retrieve singleton instance
     *
     * @param string      $type
     * @param string|null $identifier
     *
     * @return mixed
     */
    public static function getSingleton(string $type, ?string $identifier = null)
    {
        $key = $type . ($identifier ? '::' . $identifier : '');

        if (!isset(self::$singleton[$key])) {
            self::$singleton[$key] = self::getObject($type, $identifier);
        }

        return self::$singleton[$key];
    }

    /**
     * Reset singleton
     *
     * @param string|null $type
     * @param string|null $identifier
     *
     * @return void
     */
    public static function unsSingleton(?string $type = null, ?string $identifier = null): void
    {
        if ($type === null && $identifier === null) {
            self::$singleton = [];
            return;
        }

        $key = $type . ($identifier ? '::' . $identifier : '');

        if ($key && isset(self::$singleton[$key])) {
            unset(self::$singleton[$key]);
        }
    }

    /**
     * Retrieve object
     *
     * @param string      $type
     * @param string|null $identifier
     *
     * @return mixed
     */
    public static function getObject(string $type, ?string $identifier = null)
    {
        return self::getFactory()->getObject($type, $identifier);
    }

    /**
     * Retrieve factory class
     *
     * @return Core_Object_Factory
     */
    public static function getFactory(): Core_Object_Factory
    {
        if (self::$factory === null) {
            self::$factory = new Core_Object_Factory();
        }

        return self::$factory;
    }

    /**
     * Retrieve active languages
     *
     * @return string[]
     */
    public static function getLanguages(): array
    {
        return self::getConfig('app.languages');
    }

    /**
     * Retrieve domain
     *
     * @param bool|null $isSecure
     *
     * @return string
     */
    public static function getDomain(?bool $isSecure = null): string
    {
        $protocol = self::getProtocol();

        if ($isSecure !== null) {
            $protocol = self::getConfig('app.unsecured_protocol');
            if ($isSecure) {
                $protocol = self::getConfig('app.secured_protocol');
            }
        }

        return $protocol . self::getHost() . US;
    }

    /**
     * Retrieve base URL
     *
     * @param bool|null $isSecure
     *
     * @return string
     */
    public static function getBaseUrl(?bool $isSecure = null): string
    {
        return self::getDomain($isSecure);
    }

    /**
     * Retrieve root directory
     *
     * @return string
     */
    public static function getRootDir(): string
    {
        if (self::$rootDir === null) {
            return $_SERVER['DOCUMENT_ROOT'] . DS;
        }

        return self::$rootDir;
    }

    /**
     * Set Root Directory
     *
     * @param string $rootDir
     *
     * @return void
     */
    public static function setRootDir(string $rootDir): void
    {
        self::$rootDir = $rootDir;
    }

    /**
     * Retrieve current protocol
     *
     * @return string
     */
    public static function getProtocol(): string
    {
        if (self::$protocol === null) {
            self::$protocol = self::getConfig('app.unsecured_protocol');
        }

        return self::$protocol;
    }

    /**
     * Set protocol
     *
     * @param string $protocol
     *
     * @return void
     */
    public static function setProtocol(string $protocol): void
    {
        self::$protocol = $protocol;
    }

    /**
     * Retrieve config value
     *
     * @param string|null $path
     * @param mixed       $default
     *
     * @return mixed
     */
    public static function getConfig(?string $path = null, $default = null)
    {
        if (self::$config === null) {
            self::$config = new Core_Config();
            if (!self::$config->get('app.config_cache')) {
                self::$config->unlock();
            }
        }

        return self::$config->get($path) ?: $default;
    }

    /**
     * Retrieve object URL rewrite
     *
     * @param string $key
     * @param string $type
     *
     * @return string
     */
    public static function getUrlRewrite(string $key, string $type): string
    {
        $config = self::$config->get('model.rewrite.entity.' . $type);
        if (!is_array($config)) {
            return $key;
        }

        $rewrite = new Leafiny_Object($config);

        $replace = 'target';
        if (self::$config->get('model.rewrite.enabled') && $rewrite->getData('enabled')) {
            $replace = 'source';
        }

        return str_replace('*', $key, $rewrite->getData($replace));
    }

    /**
     * Retrieve translated string
     *
     * @param string|null $key
     *
     * @return string
     */
    public static function translate(?string $key): string
    {
        if ($key === null) {
            return '';
        }

        /** @var Core_Translate $translate */
        $translate = self::getSingleton('translate');

        return $translate->get($key);
    }

    /**
     * Retrieve environment
     *
     * @return string
     */
    public static function getEnvironment(): string
    {
        if (self::$environment === null) {
            self::$environment = isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'default';
        }

        return self::$environment;
    }

    /**
     * Set Environment
     *
     * @param string $environment
     *
     * @return void
     */
    public static function setEnvironment(string $environment): void
    {
        self::$environment = $environment;
    }

    /**
     * Retrieve language
     *
     * @return string
     */
    public static function getLanguage(): string
    {
        if (self::$language === null) {
            self::$language = isset($_SERVER['LANGUAGE']) ? $_SERVER['LANGUAGE'] : 'en_US';
        }

        return self::$language;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return void
     */
    public static function setLanguage(string $language): void
    {
        self::$language = $language;
    }

    /**
     * Retrieve Referer URL
     *
     * @return string|null
     */
    public static function getRefererUrl(): ?string
    {
        $referer = null;

        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = (string)$_SERVER['HTTP_REFERER'];
        }

        return $referer;
    }

    /**
     * Retrieve current host
     *
     * @return string
     */
    public static function getHost(): string
    {
        return (string)$_SERVER['HTTP_HOST'];
    }

    /**
     * Retrieve request Uri
     *
     * @return string
     */
    public static function getRequestUri(): string
    {
        return (string)$_SERVER['REQUEST_URI'];
    }

    /**
     * Retrieve Server port
     *
     * @return string
     */
    public static function getServerPort(): string
    {
        return (string)$_SERVER['SERVER_PORT'];
    }

    /**
     * Retrieve Current IP
     *
     * @return string
     */
    public static function getCurrentIp(): string
    {
        return isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '';
    }

    /**
     * SSL is active
     *
     * @return bool
     */
    public static function isSsl(): bool
    {
        return self::getServerPort() === (string)self::getConfig('app.secured_port');
    }

    /**
     * Dispatch event
     *
     * @param string $name
     * @param array  $data
     *
     * @return void
     */
    public static function dispatchEvent(string $name, array $data = []): void
    {
        /* @var array $events */
        $events = self::getConfig('events.' . $name);

        if (is_array($events)) {
            $events = array_filter($events, 'strlen');
            asort($events);
            foreach ($events as $identifier => $position) {
                if ($identifier === 0) {
                    continue;
                }

                /** @var Core_Observer $object */
                $observer = App::getObject('observer', $identifier);
                $object = new Leafiny_Object();
                foreach ($data as $key => $value) {
                    $object->setData($key, $value);
                }
                $object->setData('observer_position', $position);
                $observer->execute($object);
            }
        }
    }

    /**
     * Retrieve all identifiers for type (block, page, mail, model, helper)
     *
     * @param string $type
     *
     * @return string[]
     */
    public static function getIdentifiers(string $type): array
    {
        $identifiers = self::getConfig($type)[Leafiny_Config::TYPE_KEYS];

        rsort($identifiers);

        return $identifiers;
    }

    /**
     * Setup modules
     *
     * @return void
     * @throws Exception
     */
    public static function processSetup(): void
    {
        self::getObject('setup');
    }

    /**
     * Process Error
     *
     * @param Throwable $throwable
     *
     * @return void
     */
    public static function processError(Throwable $throwable): void
    {
        self::log($throwable, Core_Interface_Log::EMERG);

        header('HTTP/1.0 503 Service Unavailable');

        $message = 'There has been an error processing your request';
        $trace   = 'Contact your site administrator for more details';

        if (ini_get('display_errors') === '1') {
            $message = $throwable->getMessage();
            $trace   = $throwable->getTraceAsString();
        }

        $template = file_get_contents(self::getRootDir() . 'include' . DS . 'error.html');

        if ($template) {
            print preg_replace(['/{{ message }}/', '/{{ trace }}/'], [$message, $trace], $template);
        } else {
            print $message;
        }

        exit;
    }

    /**
     * Log data in file if class exists
     *
     * @param mixed    $data
     * @param int|null $level
     *
     * @return void
     */
    public static function log($data, int $level = Core_Interface_Log::INFO): void
    {
        /** @var Log|object $logInstance */
        $logInstance = self::getLogInstance();

        if ($logInstance) {
            $logInstance::save($data, $level);
        }
    }

    /**
     * Retrieve log instance
     *
     * @return object|null
     */
    public static function getLogInstance(): ?object
    {
        $logClassName = isset($_SERVER['log_class_name']) ? $_SERVER['log_class_name'] : 'Log';

        if (class_exists($logClassName)) {
            return new $logClassName;
        }

        return null;
    }

    /**
     * Throw error if requirements are not met
     *
     * @throws Exception
     */
    public static function requirements(): void
    {
        if (!is_file(self::getRootDir() . 'etc' . DS . 'config.' . self::getEnvironment() . '.php')) {
            throw new Exception('Config file for ' . self::getEnvironment() . ' is missing in etc directory');
        }

        if (!is_writable(self::getRootDir() . 'var')) {
            throw new Exception('var directory must be writable');
        }
        if (!is_writable(self::getRootDir() . 'media')) {
            throw new Exception('media directory must be writable');
        }
        if (!is_writable(self::getRootDir() . 'pub')) {
            throw new Exception('pub directory must be writable');
        }
    }

    /**
     * Process Maintenance
     *
     * @return void
     */
    public static function processMaintenance(): void
    {
        $file = self::getRootDir() . 'maintenance.flag';

        if (!is_file($file)) {
            return;
        }

        header('HTTP/1.0 503 Service Unavailable');

        if (self::getCurrentIp()) {
            $lock  = file_get_contents($file);
            $allow = explode(',', $lock);

            if (in_array(self::getCurrentIp(), $allow)) {
                return;
            }
        }

        print file_get_contents(self::getRootDir() . 'include' . DS . 'maintenance.html');

        exit;
    }

    /**
     * Retrieve module directory
     *
     * @param string|null $module
     *
     * @return string
     */
    public static function getModulesDir(?string $module = null): string
    {
        return self::getRootDir() . self::MODULES_DIRECTORY . ($module ? DS . $module : '') . DS;
    }

    /**
     * Retrieve all current modules in directory
     *
     * @return string[]
     */
    public static function getModules(): array
    {
        if (self::$modules === null) {
            self::$modules = [];
            $exclude = ['.', '..', self::FRONTEND_MODULE_NAME, self::BACKEND_MODULE_NAME];
            $directory = self::getModulesDir();
            if (is_dir($directory)) {
                $modules = scandir($directory, SCANDIR_SORT_DESCENDING);
                foreach ($modules as $module) {
                    if (preg_match('/^_/', $module)) {
                        continue;
                    }
                    if (!is_dir($directory . $module)) {
                        continue;
                    }
                    if (in_array($module, $exclude)) {
                        continue;
                    }

                    if (preg_match('/^Leafiny_/', $module)) {
                        array_unshift(self::$modules, $module);
                        continue;
                    }

                    self::$modules[] = $module;
                }
                if (in_array(self::FRONTEND_MODULE_NAME, $modules)) {
                    self::$modules[] = self::FRONTEND_MODULE_NAME;
                }
                if (in_array(self::BACKEND_MODULE_NAME, $modules)) {
                    self::$modules[] = self::BACKEND_MODULE_NAME;
                }
            }
        }

        return self::$modules;
    }

    /**
     * Retrieve loaded classes directory
     *
     * @return string
     */
    public static function getClassSymlinksDir(): string
    {
        $directory = self::getRootDir() . 'var' . DS . self::CLASS_SYMLINKS_DIRECTORY . DS;

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        return $directory;
    }

    /**
     * Autoload
     *
     * @param string $class
     *
     * @return void
     */
    public static function autoload(string $class): void
    {
        if (preg_match('/\\\\/', $class)) {
            $class = explode('\\', $class);
        } else {
            $class = explode('_', $class);
        }

        $classFile = join(DS, $class) . '.php';

        $files = [
            self::getRootDir() . 'core' . DS . 'app' . DS . $classFile,
            self::getRootDir() . 'core' . DS . 'lib' . DS . $classFile,
            self::getClassSymlinksDir() . $classFile,
        ];

        foreach ($files as $file) {
            if (is_file($file)) {
                require_once $file;
                return;
            }
        }

        foreach (self::getModules() as $module) {
            $include = self::getModulesDir($module) . 'app' . DS . $classFile;
            if (is_file($include)) {
                $loaded = self::getClassSymlinksDir() . $classFile;
                if (is_file($loaded)) {
                    unlink($loaded);
                }
                if (!is_dir(dirname($loaded))) {
                    mkdir(dirname($loaded), 0777, true);
                }
                symlink($include, $loaded);

                require_once $loaded;
                return;
            }
        }
    }
}

spl_autoload_register(['App', 'autoload']);
