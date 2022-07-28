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

class Social_Block_Comment extends Core_Block
{
    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityType(): string
    {
        return $this->getCustom('entity_type');
    }

    /**
     * Retrieve entity key
     *
     * @return string
     */
    public function getEntityKey(): string
    {
        return $this->getCustom('entity_key');
    }

    /**
     * Retrieve entity id
     *
     * @param Leafiny_Object $entity
     *
     * @return int
     */
    public function getEntityId(Leafiny_Object $entity): int
    {
        /** @var Social_Model_Comment $model */
        $model = App::getSingleton('model', $this->getEntityType());

        return (int)$entity->getData($model->getPrimaryKey());
    }

    /**
     * Retrieve entity comments
     *
     * @param Leafiny_Object|null $entity
     *
     * @return Leafiny_Object[]
     */
    public function getComments(?Leafiny_Object $entity): array
    {
        $comments = [];

        if (!$entity) {
            return $comments;
        }

        try {
            /** @var Social_Model_Comment $model */
            $model = App::getObject('model', 'social_comment');
            $comments = $model->getEntityComments(
                $this->getEntityId($entity),
                $this->getEntityType(),
                App::getLanguage()
            );
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $comments;
    }

    /**
     * Retrieve captcha inline format
     *
     * @param Core_Page $page
     *
     * @return string
     */
    public function getCaptchaImage(Core_Page $page): string
    {
        $captcha = new Leafiny_Captcha();

        $page->setTmpSessionData('form_code', $captcha->getText());

        return $captcha->inline();
    }

    /**
     * Retrieve form data
     *
     * @param Core_Page $page
     *
     * @return Leafiny_Object
     */
    public function getFormData(Core_Page $page): Leafiny_Object
    {
        $key = $this->getHelperComment()->getFormDataKey($this->getEntityType());

        if (!$this->getData($key)) {
            $this->setData($key, $page->getTmpSessionData($key) ?: new Leafiny_Object());
        }

        return $this->getData($key);
    }

    /**
     * Retrieve error message
     *
     * @param Core_Page $page
     *
     * @return string|null
     */
    public function getErrorMessage(Core_Page $page): ?string
    {
        return $page->getTmpSessionData(
            $this->getHelperComment()->getFormErrorKey($this->getEntityType())
        );
    }

    /**
     * Retrieve success message
     *
     * @param Core_Page $page
     *
     * @return string|null
     */
    public function getSuccessMessage(Core_Page $page): ?string
    {
        return $page->getTmpSessionData(
            $this->getHelperComment()->getFormSuccessKey($this->getEntityType())
        );
    }

    /**
     * Retrieve helper comment
     *
     * @return Social_Helper_Comment
     */
    public function getHelperComment(): Social_Helper_Comment
    {
        /** @var Social_Helper_Comment $helper */
        $helper = $this->getHelper('social_comment');

        return $helper;
    }
}
