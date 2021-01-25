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
 * Class Core_Template_Abstract
 */
abstract class Core_Template_Abstract extends Leafiny_Object
{
    /**
     * Context default
     *
     * @var string CONTEXT_DEFAULT
     */
    public const CONTEXT_DEFAULT = 'frontend';

    /**
     * Cache Twig Directory
     *
     * @var string CACHE_TWIG_DIRECTORY
     */
    public const CACHE_TWIG_DIRECTORY = 'twig';

    /**
     * Default context
     *
     * @var string $context
     */
    protected $context = self::CONTEXT_DEFAULT;

    /**
     * Avoid to render when this var is false
     *
     * @var bool $render
     */
    protected $render = true;

    /**
     * Environment
     *
     * @var null|Twig\Environment
     */
    protected $environment = null;

    /**
     * Retrieve Twig Environment
     *
     * @param array         $options
     * @param string[]|null $paths
     *
     * @return Twig\Environment
     */
    public function getEnvironment(array $options = [], ?array $paths = null): Twig\Environment
    {
        if (is_null($this->environment)) {
            if ($paths === null) {
                $paths = [
                    $this->getHelper()->getModulesDir(),
                    $this->getHelper()->getCacheDir(),
                ];
            }

            if (App::getConfig('app.twig_cache')) {
                $options['cache'] = $this->getHelper()->getCacheDir() . self::CACHE_TWIG_DIRECTORY . DS;
            }

            $loader = new Twig\Loader\FilesystemLoader($paths);
            $twig = new Twig\Environment($loader, $options);
            $this->addFilters($twig);
            $this->addFunctions($twig);
            $this->addExtensions($twig);

            $this->environment = $twig;
        }

        return $this->environment;
    }

    /**
     * Clear file template cache
     *
     * @param string                $template
     * @param Twig\Environment|null $environment
     *
     * @return void
     */
    public function clearTplCache(string $template, ?Twig\Environment $environment = null): void
    {
        if ($environment === null) {
            $environment = $this->getEnvironment();
        }

        $class = $environment->getTemplateClass($template);
        $file  = $environment->getCache(false)->generateKey($template, $class);

        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * Render HTML
     *
     * @param array         $options
     * @param string[]|null $paths
     *
     * @return string
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    public function render(array $options = [], ?array $paths = null): string
    {
        if (!$this->canRender()) {
            return '';
        }

        if ($this->getTemplate() === null) {
            return '';
        }

        $twig = $this->getEnvironment($options, $paths);

        if (method_exists($this, 'preRender')) {
            $this->preRender();
        }
        App::dispatchEvent(
            $this->getObjectType() . '_render_before',
            ['object' => $this]
        );

        $result = $twig->render($this->getTemplate(), [$this->getObjectType() => $this]);

        $this->setData('render', $result);

        if (method_exists($this, 'postRender')) {
            $this->postRender();
        }
        App::dispatchEvent(
            $this->getObjectType() . '_render_after',
            ['object' => $this]
        );

        return $this->getData('render');
    }

    /**
     * Add Filter to twig
     *
     * @param Twig\Environment $twig
     *
     * @return void
     */
    public function addFilters(Twig\Environment $twig): void
    {
        $filters = App::getConfig('app.twig_filters');

        if (is_array($filters)) {
            foreach ($filters as $class) {
                new $class($twig);
            }
        }

        /* Required filters */
        $twig->addFilter(new Twig\TwigFilter('translate', [$this, 'translate']));
    }

    /**
     * Add functions to twig
     *
     * @param Twig\Environment $twig
     *
     * @return void
     */
    public function addFunctions(Twig\Environment $twig): void
    {
        $functions = App::getConfig('app.twig_functions');

        if (is_array($functions)) {
            foreach ($functions as $class) {
                new $class($twig);
            }
        }

        /* Required functions */
        $twig->addFunction(new Twig\TwigFunction('child', [$this, 'blockHtml']));
    }

    /**
     * Add twig extension
     *
     * @param Twig\Environment $twig
     *
     * @return void
     */
    public function addExtensions(Twig\Environment $twig): void
    {
        $extensions = App::getConfig('app.twig_extensions');

        if (is_array($extensions)) {
            foreach ($extensions as $class) {
                new $class($twig);
            }
        }

        /* Required Extensions */
        $twig->addExtension(new Twig\Extension\StringLoaderExtension());
    }

    /**
     * Check render
     *
     * @return bool
     */
    public function canRender(): bool
    {
        return $this->render;
    }

    /**
     * Set process flag
     *
     * @param bool $render
     */
    public function setRender(bool $render): void
    {
        $this->render = $render;
    }

    /**
     * Translate
     *
     * @param string|null $key
     *
     * @return string
     */
    public function translate(?string $key): string
    {
        if ($key === null) {
            return '';
        }

        return App::translate($key) ?: $key;
    }

    /**
     * Retrieve Object Type (page, block, mail, helper...)
     *
     * @return string|null
     */
    public function getObjectType(): ?string
    {
        return $this->getData('object_type');
    }

    /**
     * Retrieve object identifier
     *
     * @return string|null
     */
    public function getObjectIdentifier(): ?string
    {
        return $this->getData('object_identifier');
    }

    /**
     * Retrieve object parameters
     *
     * @return Leafiny_Object
     */
    public function getObjectParams(): Leafiny_Object
    {
        return $this->getData('object_params');
    }

    /**
     * Retrieve object key
     *
     * @return string|null
     */
    public function getObjectKey(): ?string
    {
        return $this->getData('object_key');
    }

    /**
     * Retrieve Parent Object key
     *
     * @return string|null
     */
    public function getParentObjectKey(): ?string
    {
        return $this->getData('parent_object_key');
    }

    /**
     * Set Parent Object Key
     *
     * @param string|null $objectKey
     *
     * @return void
     */
    public function setParentObjectKey(?string $objectKey): void
    {
        $this->setData('parent_object_key', $objectKey);
    }

    /**
     * Retrieve Parent Object Identifier
     *
     * @return string|null
     */
    public function getParentObjectIdentifier(): ?string
    {
        return $this->getData('parent_object_identifier');
    }

    /**
     * Set Parent Object Key
     *
     * @param string|null $objectIdentifier
     *
     * @return void
     */
    public function setParentObjectIdentifier(?string $objectIdentifier): void
    {
        $this->setData('parent_object_identifier', $objectIdentifier);
    }

    /**
     * Retrieve Parent Object Params
     *
     * @return Leafiny_Object
     */
    public function getParentObjectParams(): Leafiny_Object
    {
        return $this->getData('parent_object_params');
    }

    /**
     * Set Parent Object Params
     *
     * @param Leafiny_Object $objectParams
     *
     * @return void
     */
    public function setParentObjectParams(Leafiny_Object $objectParams): void
    {
        $this->setData('parent_object_params', $objectParams);
    }

    /**
     * Retrieve template file
     *
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        $template = $this->getCustom('template');

        if (!$template) {
            return null;
        }

        if (preg_match('/^(?P<module>.*)::(?P<template>.*)$/', $template, $match)) {
            $template = $match['module'] . DS . 'template' . DS . $match['template'];
        }

        return $template;
    }

    /**
     * Retrieve File
     *
     * @param string $path
     * @param string $type
     * @param bool   $crypt
     *
     * @return string
     */
    public function getModuleFile(string $path, string $type, bool $crypt = false): string
    {
        /** @var Core_Helper $helper */
        $helper = App::getObject('helper');

        return $helper->getModuleFile($path, $type, $crypt);
    }

    /**
     * Retrieve block
     *
     * @param string $identifier
     * @param array  $vars
     *
     * @return Core_Block|null
     * @throws Exception
     */
    public function getBlock(string $identifier, array $vars = []): ?Core_Block
    {
        /** @var Core_Block $block */
        $block = App::getSingleton('block', $identifier);

        if (!$block->getCustom('template')) {
            return null;
        }

        foreach ($vars as $var => $value) {
            $block->setCustom($var, $value);
        }

        $block->setCurrentContext($this->getContext());

        if (!$block->getTemplate()) {
            return null;
        }

        $block->setParentObjectIdentifier($this->getObjectIdentifier());
        $block->setParentObjectKey($this->getObjectKey());
        $block->setParentObjectParams($this->getObjectParams());

        return $block;
    }

    /**
     * Show block template
     *
     * @param string $identifier
     * @param array  $vars
     *
     * @return void
     * @throws Exception
     */
    public function blockHtml(string $identifier, array $vars = []): void
    {
        $block = $this->getBlock($identifier, $vars);

        if ($block) {
            try {
                $environment = $this->getEnvironment();
                $environment->display($block->getTemplate(), ['page' => $this, 'block' => $block]);
            } catch (Twig\Error\LoaderError $exception) {
                // Ignore missing template
            }
        }
    }

    /**
     * Retrieve current context
     *
     * @return string
     */
    public function getContext(): string
    {
        return $this->getCustom('context') ?: $this->context;
    }

    /**
     * Force current context
     *
     * @param string $context
     *
     * @return void
     */
    public function setContext(string $context): void
    {
        $this->context = $context;
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
     * Get custom data
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Core_Template_Abstract
     */
    public function setCustom(string $key, $value): Core_Template_Abstract
    {
        /** @var Leafiny_Object $custom */
        $custom = $this->getData(Core_Object_Factory::CUSTOM_KEY);

        $custom->setData($key, $value);

        return $this;
    }

    /**
     * Retrieve config
     *
     * @param string $path
     *
     * @return string|null
     */
    public function getConfig(string $path): ?string
    {
        return App::getConfig($path);
    }

    /**
     * Retrieve media URL
     *
     * @param string|null $path
     *
     * @return string
     */
    public function getMediaUrl(?string $path = null): string
    {
        if ($path === null) {
            return $this->getHelper()->getMediaUrl();
        }

        return $this->getHelper()->getModuleUrl($this->getModuleFile($path, Core_Helper::MEDIA_DIRECTORY, true));
    }

    /**
     * Retrieve skin Url
     *
     * @param string|null $path
     *
     * @return string
     */
    public function getSkinUrl(?string $path = null): string
    {
        return $this->getHelper()->getModuleUrl($this->getModuleFile($path, Core_Helper::SKIN_DIRECTORY, true));
    }

    /**
     * Retrieve current domain
     *
     * @param bool|null $isSecure
     *
     * @return string
     */
    public function getDomain(?bool $isSecure = null): string
    {
        return App::getDomain($isSecure);
    }

    /**
     * Retrieve URL with path
     *
     * @param string|null $page
     * @return string
     */
    public function getUrl(?string $page = null): string
    {
        if ($page === null) {
            $page = '/';
        }

        $page = $this->getIdentifierPath($page);
        $page = ltrim($page, '/');

        return $this->getBaseUrl() . $page;
    }

    /**
     * Retrieve identifier path
     *
     * @param string $path
     *
     * @return string
     */
    public function getIdentifierPath(string $path): string
    {
        $objectKey = $this->getParentObjectKey() ?: $this->getObjectKey();

        if ($objectKey !== null) {
            $path = preg_replace('/\*/', $objectKey, $path);
        }

        return $path;
    }

    /**
     * Retrieve current base URL
     *
     * @param bool|null $isSecure
     *
     * @return string
     */
    public function getBaseUrl(?bool $isSecure = null): string
    {
        return App::getBaseUrl($isSecure);
    }

    /**
     * Retrieve active languages
     *
     * @return string[]
     */
    public function getLanguages(): array
    {
        return App::getLanguages();
    }

    /**
     * Retrieve Helper
     *
     * @param string|null $identifier
     *
     * @return Core_Helper
     */
    public function getHelper(?string $identifier = null): Core_Helper
    {
        return App::getSingleton('helper', $identifier);
    }
}
