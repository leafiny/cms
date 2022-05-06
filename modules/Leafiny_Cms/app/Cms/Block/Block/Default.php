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
 * Class Cms_Block_Block_Default
 */
class Cms_Block_Block_Default extends Core_Block
{
    /**
     * Retrieve CMS Block
     *
     * @return Leafiny_Object
     */
    public function getCmsBlock(): Leafiny_Object
    {
        return $this->getCustom('cms_block');
    }
}
