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
 * Class Core_Helper
 */
class Core_Helper extends Leafiny_Object
{
    /**
     * @var string CONFIG_DIRECTORY
     */
    public const CONFIG_DIRECTORY = 'etc';
    /**
     * @var string TEMPLATE_DIRECTORY
     */
    public const TEMPLATE_DIRECTORY = 'template';
    /**
     * @var string SETUP_DIRECTORY
     */
    public const SETUP_DIRECTORY = 'setup';
    /**
     * @var string APP_DIRECTORY
     */
    public const APP_DIRECTORY = 'app';
    /**
     * @var string SKIN_DIRECTORY
     */
    public const SKIN_DIRECTORY = 'skin';
    /**
     * @var string MEDIA_DIRECTORY
     */
    public const MEDIA_DIRECTORY = 'media';
    /**
     * @var string PUB_DIRECTORY
     */
    public const PUB_DIRECTORY = 'pub';
    /**
     * @var string I18N_DIRECTORY
     */
    public const I18N_DIRECTORY = 'i18n';
    /**
     * @var string VAR_DIRECTORY
     */
    public const VAR_DIRECTORY = 'var';

    /**
     * Salt Key
     *
     * @var string|null $saltKey
     */
    protected $saltKey = null;

    /**
     * Retrieve module file path
     *
     * @param string $path
     * @param string $type
     * @param bool   $crypt
     *
     * @return string
     */
    public function getModuleFile(string $path, string $type, bool $crypt = false): string
    {
        if (preg_match('/^(?P<module>.*)::(?P<file>.*)$/', $path, $match)) {
            $module = $match['module'];
            $salt = $this->getDeploymentKey();
            if ($crypt && !empty($salt)) {
                $module = md5($salt . $module);
            }
            $path = $module . DS . $type . DS . $match['file'];
        }

        return $path;
    }

    /**
     * Retrieve deployment key
     *
     * @return string
     */
    public function getDeploymentKey(): string
    {
        if ($this->saltKey === null) {
            $file = $this->getCryptDir() . 'deploy.key';
            $this->saltKey = '';
            if (is_file($file)) {
                $this->saltKey = file_get_contents($file) ?: '';
            }
        }

        return $this->saltKey;
    }

    /**
     * Retrieve module url
     *
     * @param string $path
     *
     * @return string
     */
    public function getModuleUrl(string $path): string
    {
        return App::getDomain() . App::MODULES_DIRECTORY . US . str_replace(DS, US, $path);
    }

    /**
     * Retrieve root media URL
     *
     * @return string
     */
    public function getMediaUrl(): string
    {
        return App::getDomain() . self::MEDIA_DIRECTORY . US;
    }

    /**
     * Retrieve template directory
     *
     * @param string $module
     *
     * @return string
     */
    public function getTemplateDir(string $module): string
    {
        return $this->getModulesDir($module) . self::TEMPLATE_DIRECTORY . DS;
    }

    /**
     * Retrieve config directory
     *
     * @param string $module
     *
     * @return string
     */
    public function getConfigDir(string $module): string
    {
        return $this->getModulesDir($module) . self::CONFIG_DIRECTORY . DS;
    }

    /**
     * Retrieve app dir
     *
     * @param string $module
     *
     * @return string
     */
    public function getAppDir(string $module): string
    {
        return $this->getModulesDir($module) . self::APP_DIRECTORY . DS;
    }

    /**
     * Retrieve setup dir
     *
     * @param string $module
     *
     * @return string
     */
    public function getTranslateDir(string $module): string
    {
        return $this->getModulesDir($module) . self::I18N_DIRECTORY . DS;
    }

    /**
     * Retrieve setup dir
     *
     * @param string $module
     *
     * @return string
     */
    public function getSetupDir(string $module): string
    {
        return $this->getModulesDir($module) . self::SETUP_DIRECTORY . DS;
    }

    /**
     * Retrieve var directory
     *
     * @return string
     */
    public function getVarDir(): string
    {
        return App::getRootDir() . self::VAR_DIRECTORY . DS;
    }

    /**
     * Retrieve root media dir
     *
     * @return string
     */
    public function getMediaDir(): string
    {
        return App::getRootDir() . self::MEDIA_DIRECTORY . DS;
    }

    /**
     * Retrieve crypt directory
     *
     * @return string
     */
    public function getCryptDir(): string
    {
        $crypt = $this->getVarDir() . DS . 'crypt' . DS;

        if (!is_dir($crypt)) {
            mkdir($crypt);
        }

        return $crypt;
    }

    /**
     * Retrieve session directory
     *
     * @return string
     */
    public function getSessionDir(): string
    {
        $directory = $this->getVarDir() . 'session' . DS;

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        return $directory;
    }

    /**
     * Retrieve cache directory
     *
     * @return string
     */
    public function getCacheDir(): string
    {
        $directory = $this->getVarDir() . 'cache' . DS;

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        return $directory;
    }

    /**
     * Retrieve cache directory
     *
     * @return string
     */
    public function getLockDir(): string
    {
        $directory = $this->getVarDir() . 'lock' . DS;

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        return $directory;
    }

    /**
     * Retrieve Environment Config File
     *
     * @return string
     */
    public function getEnvironmentConfigFile(): string
    {
        return App::getRootDir() . self::CONFIG_DIRECTORY . DS . 'config.' . App::getEnvironment() . '.php';
    }

    /**
     * Retrieve module config file
     *
     * @param string $module
     *
     * @return string
     */
    public function getModelConfigFile(string $module): string
    {
        return $this->getModulesDir($module) . self::CONFIG_DIRECTORY . DS . 'config.php';
    }

    /**
     * Retrieve module directory
     *
     * @param string|null $module
     *
     * @return string
     */
    public function getModulesDir(?string $module = null): string
    {
        return App::getModulesDir($module);
    }

    /**
     * Retrieve all current modules in directory
     *
     * @return string[]
     */
    public function getModules(): array
    {
        return App::getModules();
    }

    /**
     * Format a key with special chars replacement
     *
     * @param string   $value
     * @param string[] $toRemove
     * @param string[] $toKeep
     * @param string   $replace
     *
     * @return string
     */
    public function formatKey(string $value, array $toRemove = [], array $toKeep = [], string $replace = '-'): string
    {
        $key = new Leafiny_Key();

        return $key->format($value, $replace, $toKeep, $toRemove);
    }

    /**
     * Replace all strings between double curly braces by the data object associated value
     *
     * @param string|null    $content
     * @param Leafiny_Object $object
     *
     * @return string
     */
    public function replaceStrWithDataObject(?string $content, Leafiny_Object $object): string
    {
        if ($content === null) {
            return '';
        }

        foreach ($object->getData() as $field => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }
            $content = str_replace('{{' . $field . '}}', (string) $value, $content);
        }

        $content = preg_replace('/{{(.*)}}/', '', $content);
        $content = preg_replace('/\s+/', ' ', $content);

        return trim($content);
    }

    /**
     * Process dynamic metadata from object
     *
     * @param mixed[]        $metadata
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function dynamicMetadata(array $metadata, Leafiny_Object $object): void
    {
        foreach ($metadata as $field => $value) {
            if ($object->getData($field)) {
                $value = $object->getData($field);
            }
            $object->setData($field, $this->replaceStrWithDataObject((string) $value, $object));
        }
    }

    /**
     * Retrieve date in locale
     *
     * @param string $date
     * @param string $format
     *
     * @return string
     */
    public function getDate(string $date, string $format): string
    {
        return strftime($format, strtotime($date));
    }

    /**
     * Get custom data
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getCustom(string $key)
    {
        /** @var Leafiny_Object $custom */
        $custom = $this->getData(Core_Object_Factory::CUSTOM_KEY);

        if (!$custom) {
            return null;
        }

        return $custom->getData($key);
    }

    /**
     * Set custom data
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Core_Helper
     */
    public function setCustom(string $key, $value): Core_Helper
    {
        /** @var Leafiny_Object $custom */
        $custom = $this->getData(Core_Object_Factory::CUSTOM_KEY);

        $custom->setData($key, $value);

        return $this;
    }
}
