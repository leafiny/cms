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
 * Class Contact_Page_Contact
 */
class Contact_Page_Contact extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $data = $this->getTmpSessionData('form_post_data');
        $this->setData('form', $data);
        $this->setCustom('breadcrumb', $this->getBreadcrumb());
    }

    /**
     * Retrieve captcha inline format
     *
     * @return string
     */
    public function getCaptchaImage(): string
    {
        $captcha = new Leafiny_Captcha;

        $this->setTmpSessionData('form_code', $captcha->getText());

        return $captcha->inline();
    }

    /**
     * Retrieve breadcrumb
     *
     * @return string[]
     */
    public function getBreadcrumb(): array
    {
        if ($this->getCustom('hide_breadcrumb')) {
            return [];
        }

        $title = $this->getCustom('title');
        if (!$title) {
            return [];
        }

        return [
            $this->translate($title) => $this->getBaseUrl() . ltrim($this->getObjectIdentifier(), '/')
        ];
    }

    /**
     * Retrieve object key
     *
     * @return string|null
     */
    public function getObjectKey(): ?string
    {
        return 'contact';
    }
}
