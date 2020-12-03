<?php

declare(strict_types=1);

/**
 * Class Blog_Block_Post_Comments
 */
class Blog_Block_Post_Comments extends Core_Block
{
    /**
     * Retrieve post comments
     *
     * @param int $postId
     *
     * @return Leafiny_Object[]
     */
    public function getComments(int $postId): array
    {
        /** @var Blog_Model_Post $post */
        $post = App::getObject('model', 'blog_post');

        $comments = [];

        try {
            $commentIds = $post->getComments($postId);
            if (empty($commentIds)) {
                return $comments;
            }

            /** @var Social_Model_Comment $model */
            $model = App::getObject('model', 'social_comment');

            $comments = $model->getList($this->getFilters($commentIds), $this->getSortOrders());
        } catch (Throwable $throwable) {
            /** @var Log_Model_File $log */
            $log = App::getObject('model', 'log_file');
            $log->add($throwable->getMessage());
            return [];
        }

        return $comments;
    }

    /**
     * Retrieve comment filters
     *
     * @param array $commentIds
     *
     * @return array[]
     */
    public function getFilters(array $commentIds): array
    {
        return [
            [
                'column'   => 'comment_id',
                'value'    => $commentIds,
                'operator' => 'IN',
            ],
            [
                'column'   => 'status',
                'value'    => 1,
                'operator' => '=',
            ],
            [
                'column'   => 'language',
                'value'    => App::getLanguage(),
                'operator' => '=',
            ],
        ];
    }

    /**
     * Retrieve comment sort orders
     *
     * @return string[][]
     */
    public function getSortOrders(): array
    {
        return [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ]
        ];
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
        $captcha = new Leafiny_Captcha;

        $page->setTmpSessionData('form_code', $captcha->getText());

        return $captcha->inline();
    }
}
