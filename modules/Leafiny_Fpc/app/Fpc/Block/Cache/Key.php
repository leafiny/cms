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
 * Class Fpc_Block_Cache_Key
 */
class Fpc_Block_Cache_Key extends Core_Block
{
    /**
     * Retrieve flush cache key URL
     *
     * @return string
     */
    public function getFlushKeyUrl(): string
    {
        return $this->getUrl('/admin/*/cache/flush/fpc/key/');
    }
}
