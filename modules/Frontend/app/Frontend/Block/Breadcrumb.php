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
 * Class Frontend_Block_Breadcrumb
 */
class Frontend_Block_Breadcrumb extends Core_Block
{
    /**
     * Breadcrumb links
     *
     * @var string[] $links
     */
    protected $links = [];

    /**
     * Add links
     *
     * @param string[] $links
     */
    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    /**
     * Retrieve Links
     *
     * @return string[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
