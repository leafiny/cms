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

class Frontend_Helper_Combine_Js extends Frontend_Helper_Combine
{
    /**
     * Retrieve file content
     *
     * @param Leafiny_Object $file
     *
     * @return string
     */
    public function getContent(Leafiny_Object $file): string
    {
        return parent::getContent($file);
    }
}
