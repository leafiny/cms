<?php

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

        $this->setTmpSessionData('form_comment_post_data', $form);

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
            $this->setErrorMessage($this->translate($error));
            $this->redirect($this->getRefererUrl());
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
                $success[] = $this->translate('It will be displayed after validation.');
            }

            $this->setSuccessMessage(join(' ', $success));
            $this->unsTmpSessionData('form_comment_post_data');
        } catch (Throwable $throwable) {
            /** @var Log_Model_File $log */
            $log = App::getObject('model', 'log_file');
            $log->add($throwable->getMessage());
            $this->setErrorMessage(
                $this->translate('An error occurred when adding comment')
            );
        }

        $this->redirect($this->getRefererUrl());
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
