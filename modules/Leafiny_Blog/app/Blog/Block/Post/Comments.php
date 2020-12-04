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

    /**
     * Retrieve form data
     *
     * @param Core_Page $page
     *
     * @return Leafiny_Object
     */
    public function getFormData(Core_Page $page): Leafiny_Object
    {
        $key = Blog_Helper_Data::COMMENT_FORM_DATA_KEY;

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
        return $page->getTmpSessionData(Blog_Helper_Data::COMMENT_FORM_ERROR_KEY);
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
        return $page->getTmpSessionData(Blog_Helper_Data::COMMENT_FORM_SUCCESS_KEY);
    }
}
