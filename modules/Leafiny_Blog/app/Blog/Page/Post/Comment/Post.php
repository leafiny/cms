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
 * Class Blog_Page_Post_Comment_Post
 */
class Blog_Page_Post_Comment_Post extends Core_Page
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

        $postId = $form->getData('post_id');
        if (!$postId) {
            $this->redirect($this->getRefererUrl());
        }

        /** @var Blog_Model_Post $model */
        $model = App::getObject('model', 'blog_post');
        $post = $model->get($postId);

        if (!$post->getData('post_id')) {
            $this->redirect($this->getRefererUrl());
        }

        $this->setTmpSessionData(Blog_Helper_Data::COMMENT_FORM_DATA_KEY, $form);

        /** @var Social_Model_Comment $comment */
        $comment = App::getObject('model', 'social_comment');
        $error = $comment->validate($form);

        if ($this->isFormCodeRequired()) {
            $formCode = $this->getTmpSessionData('form_code') ?: '';
            if (empty($formCode) || strtolower($formCode) !== strtolower($form->getData('form_code') ?: '')) {
                $error = 'Invalid security code';
            }
        }

        if (!empty($error)) {
            $this->setTmpSessionData(Blog_Helper_Data::COMMENT_FORM_ERROR_KEY, $this->translate($error));
            $this->redirect($this->getRefererUrl() . '#comment-form');
        }

        try {
            $data = new Leafiny_Object();
            $data->setData(
                [
                    'name'    => $form->getData('name'),
                    'email'   => $form->getData('email'),
                    'comment' => $form->getData('comment'),
                    'referer' => $post->getData('title'),
                ]
            );
            $commentId = $comment->save($data);

            $model->addComment((int)$postId, $commentId);

            $data->setData('link', $this->getUrl($post->getData('path_key') . '.html'));
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

            $this->setTmpSessionData(Blog_Helper_Data::COMMENT_FORM_SUCCESS_KEY, join(' ', $success));
            $this->unsTmpSessionData(Blog_Helper_Data::COMMENT_FORM_DATA_KEY);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->setTmpSessionData(
                Blog_Helper_Data::COMMENT_FORM_ERROR_KEY,
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
