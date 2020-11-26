<?php

declare(strict_types=1);

/**
 * Class Core_Session
 */
class Core_Session extends Leafiny_Object
{
    /**
     * Session
     *
     * @var Leafiny_Session $session
     */
    protected $session = null;

    /**
     * Init Session
     *
     * @param string $context
     *
     * @return void
     * @throws Exception
     */
    public function init(string $context = ''): void
    {
        if ($this->session !== null) {
            return;
        }

        $dir = $this->getHelper()->getSessionDir();
        if (is_writable($dir)) {
            session_save_path($dir);
        }

        $session = new Leafiny_Session();
        $session->init($this->getLifeTime(), $this->getSameSite(), $this->getDomain(), App::isSsl(), $context);

        $this->session = $session;
    }

    /**
     * Retrieve current session
     *
     * @return Leafiny_Session|null
     */
    public function getSession(): ?Leafiny_Session
    {
        return $this->session;
    }

    /**
     * Retrieve lifetime
     *
     * @return int
     */
    public function getLifeTime(): int
    {
        $lifetime = $this->getCustom('lifetime');

        return $lifetime ? (int)$lifetime : 0;
    }

    /**
     * Retrieve Same Site
     *
     * @return string
     */
    public function getSameSite(): ?string
    {
        $sameSite = $this->getCustom('samesite');

        return $sameSite ? (string)$sameSite : 'strict';
    }

    /**
     * Retrieve Domain
     *
     * @return string
     */
    public function getDomain(): ?string
    {
        $domain = $this->getCustom('domain');

        return $domain ? (string)$domain : '';
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
        $custom = $this->getData('custom');

        if (!$custom) {
            return null;
        }

        return $custom->getData($key);
    }

    /**
     * Retrieve Helper
     *
     * @return Core_Helper
     */
    public function getHelper(): Core_Helper
    {
        return new Core_Helper;
    }
}
