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
 * Class Cms_Helper_Cms
 */
class Cms_Helper_Cms extends Core_Helper
{
    /**
     * Secure child blocks
     *
     * @param Leafiny_Object $block
     */
    public function secureChildBlocks(Leafiny_Object $block): void
    {
        $content = $block->getData('content');

        if (!$content) {
            return;
        }

        $identifier = 'block.static::' . $block->getData('path_key');
        $pattern = '/{{ (\'|")' . $identifier . '(\'|")\|block }}/';

        if (!preg_match($pattern, $content)) {
            return;
        }

        $content = preg_replace($pattern, '', $content);

        $block->setData('content', $content);
    }
}
