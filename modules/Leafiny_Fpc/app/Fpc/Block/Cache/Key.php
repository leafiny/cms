<?php

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
