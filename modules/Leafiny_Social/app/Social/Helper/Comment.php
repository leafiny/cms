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

class Social_Helper_Comment extends Core_Helper
{
    /**
     * Retrieve form data key
     *
     * @param string $entityType
     *
     * @return string
     */
    public function getFormDataKey(string $entityType): string
    {
        return 'form_comment_data_' . $entityType;
    }

    /**
     * Retrieve form error key
     *
     * @param string $entityType
     *
     * @return string
     */
    public function getFormErrorKey(string $entityType): string
    {
        return 'form_comment_error' . $entityType;
    }

    /**
     * Retrieve form success key
     *
     * @param string $entityType
     *
     * @return string
     */
    public function getFormSuccessKey(string $entityType): string
    {
        return 'form_comment_success' . $entityType;
    }
}
