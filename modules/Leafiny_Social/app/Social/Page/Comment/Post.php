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

class Social_Page_Comment_Post extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        $form = $this->getPost();

        if (!$form->hasData()) {
            $this->redirect($this->getRefererUrl());
        }

        $entityId = $form->getData('entity_id');
        if (!$entityId) {
            $this->redirect($this->getRefererUrl());
        }

        $entityType = $form->getData('entity_type');
        if (!$entityType) {
            $this->redirect($this->getRefererUrl());
        }

        /** @var Core_Helper_Crypt $encrypt */
        $encrypt = App::getObject('helper_crypt');

        $entityId = $encrypt->decrypt($entityId);
        $entityType = $encrypt->decrypt($entityType);

        /** @var Core_Model $model */
        $model = App::getObject('model', $entityType);
        $entity = $model->get($entityId);

        if (!$entity->getData($model->getPrimaryKey())) {
            $this->redirect($this->getRefererUrl());
        }

        /** @var Social_Helper_Comment $helperComment */
        $helperComment = App::getSingleton('helper', 'social_comment');

        $this->setTmpSessionData($helperComment->getFormDataKey($entityType), $form);

        /** @var Social_Model_Comment $comment */
        $comment = App::getSingleton('model', 'social_comment');
        $error = $comment->validate($form);

        if ($this->isFormCodeRequired()) {
            $formCode = $this->getTmpSessionData('form_code') ?: '';
            if (empty($formCode) || strtolower($formCode) !== strtolower($form->getData('form_code') ?: '')) {
                $error = 'Invalid security code';
            }
        }

        if (!empty($error)) {
            $this->setTmpSessionData($helperComment->getFormErrorKey($entityType), $this->translate($error));
            $this->redirect($this->getRefererUrl() . '#comment-form');
        }

        try {
            $data = new Leafiny_Object();
            $data->setData(
                [
                    'entity_id'   => $entityId,
                    'entity_type' => $entityType,
                    'name'        => $form->getData('name'),
                    'email'       => $form->getData('email'),
                    'comment'     => $form->getData('comment'),
                    'note'        => $form->getData('note'),
                    'referer'     => $entity->getData('title') ?: $entity->getData('name'),
                ]
            );
            if ($entity->getData('path_key')) {
                $data->setData('link', $this->getUrlRewrite($entity->getData('path_key'), $entityType));
            }

            $comment->save($data);

            $data->setData('status', $comment->getDefaultStatus());

            /** @var Core_Mail $mail */
            $mail = App::getObject('mail', 'new_comment');
            $mail->send(['comment' => $data]);

            $success = [
                $this->translate('Your comment was added successfully.')
            ];
            if (!$comment->getDefaultStatus()) {
                $success = [
                    $this->translate('Your comment was submitted successfully.'),
                    $this->translate('It will be displayed after validation.')
                ];
            }

            $this->setTmpSessionData($helperComment->getFormSuccessKey($entityType), join(' ', $success));
            $this->unsTmpSessionData($helperComment->getFormDataKey($entityType));
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->setTmpSessionData(
                $helperComment->getFormErrorKey($entityType),
                $this->translate('An error occurred when adding comment')
            );
            $this->redirect($this->getRefererUrl() . '#comment-form');
        }

        $this->redirect($this->getRefererUrl() . '#comments');
    }

    /**
     * Is Form code required
     *
     * @return bool
     */
    public function isFormCodeRequired(): bool
    {
        return (bool)$this->getCustom('form_code_required');
    }
}
