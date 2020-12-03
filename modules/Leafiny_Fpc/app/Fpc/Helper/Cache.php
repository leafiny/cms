<?php

declare(strict_types=1);

/**
 * Class Fpc_Helper_Cache
 */
class Fpc_Helper_Cache extends Core_Helper
{
    /**
     * @var string CACHE_FPC_DIRECTORY
     */
    public const CACHE_FPC_DIRECTORY = 'fpc';

    /**
     * Check if page can be cached
     *
     * @param Core_Page $page
     * @return bool
     */
    public function canCache(Core_Page $page): bool
    {
        if (!$page->getTemplate()) {
            return false;
        }

        if ($page->getContext() !== Core_Template_Abstract::CONTEXT_DEFAULT) {
            return false;
        }

        if (!$page->getCustom('fpc')) {
            return false;
        }

        if (in_array($page->getObjectIdentifier(), $this->getNoCacheIdentifiers())) {
            return false;
        }

        if (in_array($page->getObjectKey(), $this->getNoCacheKeys())) {
            return false;
        }

        $params = $page->getObjectParams();

        if ($params->hasData()) {
            foreach (array_keys($params->getData()) as $param) {
                if (in_array($param, $this->getAllowedParams())) {
                    continue;
                }
                return false;
            }
        }

        return true;
    }

    /**
     * No FPC for specific identifiers
     *
     * @return string[]
     */
    public function getAllowedParams(): array
    {
        if (is_array($this->getCustom('allowed_params'))) {
            return $this->getCustom('allowed_params');
        }

        return [];
    }

    /**
     * No FPC for specific identifiers
     *
     * @return string[]
     */
    public function getNoCacheIdentifiers(): array
    {
        if (is_array($this->getCustom('no_cache_identifiers'))) {
            return $this->getCustom('no_cache_identifiers');
        }

        return [];
    }

    /**
     * No FPC for specific keys
     *
     * @return string[]
     */
    public function getNoCacheKeys(): array
    {
        if (is_array($this->getCustom('no_cache_keys'))) {
            return $this->getCustom('no_cache_keys');
        }

        return [];
    }

    /**
     * Retrieve FPC cache directory
     *
     * @return string
     */
    public function getFpcCacheDir(): string
    {
        $directory = parent::getCacheDir() . self::CACHE_FPC_DIRECTORY . DS;

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        return $directory;
    }

    /**
     * Retrieve cache
     *
     * @param Core_Page $page
     *
     * @return string|null
     * @throws Exception
     */
    public function getCacheFile(Core_Page $page): ?string
    {
        $directory = $this->getPageCacheDir($page->getObjectKey());

        $file = $directory . $this->getPageCacheKey($page);

        if (is_file($file)) {
            return str_replace($this->getCacheDir(), '', $file);
        }

        return null;
    }

    /**
     * Save page content in file
     *
     * @param Core_Page $page
     */
    public function saveCache(Core_Page $page): void
    {
        $directory = $this->getPageCacheDir($page->getObjectKey());

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        $file = $directory . $this->getPageCacheKey($page);

        if (!is_file($file)) {
            file_put_contents($file, $this->processBlocks($page->getData('render')));
        }
    }

    /**
     * Flush cache for object
     *
     * @param string|null $cacheKey
     *
     * @return bool
     */
    public function flushCache(?string $cacheKey): bool
    {
        $directory = $this->getPageCacheDir($cacheKey);

        $isFlushed = false;

        if (is_dir($directory)) {
            $files = scandir($directory);

            foreach ($files as $file) {
                if (!is_file($directory . $file)) {
                    continue;
                }

                $isFlushed = true;

                if (App::getConfig('app.twig_cache')) {
                    $template = str_replace($this->getCacheDir(), '', $directory . $file);
                    $page = new Core_Page();
                    $page->clearTplCache($template);
                }

                unlink($directory . $file);
            }

            if (count(glob($directory . '*')) === 0) {
                rmdir($directory);
            }
        }

        return $isFlushed;
    }

    /**
     * Process Blocks render
     *
     * @param string $render
     *
     * @return string
     */
    public function processBlocks(string $render): string
    {
        if (preg_match_all('@<!-- nocache::(?P<blocks>.*) -->@U', $render, $matches)) {
            foreach ($matches['blocks'] as $block) {
                $render = preg_replace(
                    '@<!-- nocache::' . $block . ' -->(.*)<!-- /nocache::' . $block . ' -->@s',
                    '{% apply spaceless %}{{ ' . $block . '|block }}{% endapply %}',
                    $render
                );
            }
        }

        $render = preg_replace('/<!--(.|\s)*?-->/', '', $render);

        return $render;
    }

    /**
     * Retrieve page cache directory
     *
     * @param string|null $cacheKey
     *
     * @return string
     */
    public function getPageCacheDir(?string $cacheKey): string
    {
        return $this->getFpcCacheDir() . md5($cacheKey ?: 'default') . DS;
    }

    /**
     * Retrieve cache key
     *
     * @param Core_Page $page
     *
     * @return string
     */
    public function getPageCacheKey(Core_Page $page): string
    {
        $key = [
            $page->getObjectIdentifier(),
            $page->getObjectKey(),
            $page->getObjectParams(),
            $page->getContext(),
            App::getEnvironment(),
            App::getLanguage()
        ];

        return md5(serialize($key)) . '.twig';
    }
}
