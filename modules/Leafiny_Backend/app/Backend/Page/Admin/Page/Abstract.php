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
 * Class Backend_Page_Admin_Page_Abstract
 */
abstract class Backend_Page_Admin_Page_Abstract extends Core_Page
{
    /**
     * Context default
     *
     * @var string CONTEXT_BACKEND
     */
    public const CONTEXT_BACKEND = 'backend';

    /**
     * Default context
     *
     * @var string $context
     */
    protected $context = self::CONTEXT_BACKEND;

    /**
     * Need Login
     *
     * @return bool
     */
    public function needLogin(): bool
    {
        return true;
    }

    /**
     * Retrieve backend URL
     *
     * @param string|null $page
     *
     * @return string
     */
    public function getUrl(?string $page = null): string
    {
        if ($page === null && $this->getContext() === self::CONTEXT_BACKEND) {
            $page = '/admin/*/';
        }

        return parent::getUrl($page);
    }

    /**
     * Retrieve backend helper
     *
     * @return Backend_Helper_Data
     */
    public function getBackendHelper(): Backend_Helper_Data
    {
        return App::getSingleton('helper', 'admin_data');
    }

    /**
     * Send 404 if page does not allow params in URL
     *
     * @return Core_Page
     * @throws Exception
     */
    protected function allowParams(): Core_Page
    {
        if (!$this->getAllowParams() && $this->getHasParams()) {
            $this->setContext(Backend_Page_Admin_Page_Abstract::CONTEXT_DEFAULT);
        }

        return parent::allowParams();
    }

    /**
     * Retrieve current version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return App::getVersion();
    }

    /**
     * Retrieve all countries
     *
     * @return string[]
     */
    public function getAllCountries(): array
    {
        /** @var Core_Helper_Country $helper */
        $helper = App::getObject('helper_country');

        return $helper->getList();
    }

    /**
     * Retrieve all languages
     *
     * @return string[]
     */
    public function getAllLanguages(): array
    {
        /** @var Core_Helper_Language $helper */
        $helper = App::getObject('helper_language');

        return $helper->getList();
    }

    /**
     * Retrieve active languages
     *
     * @return array
     */
    public function getActiveLanguages(): array
    {
        /** @var Core_Helper_Language $helper */
        $helper = App::getObject('helper_language');

        $actives   = $this->getLanguages();
        $languages = [];

        foreach ($actives as $code) {
            $languages[$code] = $helper->getName($code);
        }

        return $languages;
    }

    /**
     * Retrieve languages by code
     *
     * @param string $code
     *
     * @return string
     */
    public function getLanguageByCode(string $code): string
    {
        /** @var Core_Helper_Language $helper */
        $helper = App::getObject('helper_language');

        return $helper->getName($code);
    }

    /**
     * Retrieve date in locale
     *
     * @param string $date
     *
     * @return string
     */
    public function formatDate(string $date): string
    {
        return strftime('%d %b %Y %H:%M:%S', strtotime($date));
    }
}
