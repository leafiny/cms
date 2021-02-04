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
 * Class Backend_Page_Admin_Form
 */
class Backend_Page_Admin_Form extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Form data
     *
     * @var Leafiny_Object|null $formData
     */
    protected $formData = null;

    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        if ($this->formData !== null) {
            return;
        }

        $postData = $this->getTmpSessionData('form_post_data');
        if ($postData) {
            $this->formData = $postData;
            return;
        }

        $params = $this->getParams();

        if (!$params->getData('id')) {
            $this->formData = new Leafiny_Object();
            return;
        }

        try {
            $this->formData = $this->getModel()->get($params->getData('id'));
            if (!$this->formData->hasData()) {
                $this->setErrorMessage($this->translate('This element no longer exists'));
                $this->redirect($this->getRefererUrl());
            }
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
            $this->formData = new Leafiny_Object();
        }
    }

    /**
     * Retrieve Form save URL
     *
     * @return string
     */
    public function getSaveUrl(): string
    {
        return $this->getUrl($this->getObjectIdentifier() . 'save/');
    }

    /**
     * Retrieve Form data
     *
     * @return Leafiny_Object
     */
    public function getFormData(): Leafiny_Object
    {
        return $this->formData;
    }

    /**
     * Retrieve model
     *
     * @return Core_Model
     */
    public function getModel(): Core_Model
    {
        return App::getSingleton('model', $this->getModelIdentifier());
    }

    /**
     * Retrieve Model Identifier
     *
     * @return string|null
     */
    public function getModelIdentifier(): ?string
    {
        $modelIdentifier = $this->getCustom('model_identifier');

        return $modelIdentifier ? (string)$modelIdentifier : null;
    }

    /**
     * Retrieve Children blocks
     *
     * @return string[]
     */
    public function getChildren(): array
    {
        $children = $this->getCustom('children');

        if (!$children) {
            return [];
        }

        $children = array_filter($children, 'strlen');

        asort($children);

        return array_keys($children);
    }
}
