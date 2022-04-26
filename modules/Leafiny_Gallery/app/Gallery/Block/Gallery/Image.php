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
 * Class Gallery_Block_Gallery_Image
 */
class Gallery_Block_Gallery_Image extends Core_Block
{
    /**
     * Retrieve image
     *
     * @return Leafiny_Object
     */
    public function getImage(): Leafiny_Object
    {
        return $this->getCustom('image');
    }
}
