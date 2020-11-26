<?php

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
}
