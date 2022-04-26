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
 * Class Search_Block_Form
 */
class Search_Block_Form extends Core_Block
{
    /**
     * Retrieve Search Query
     *
     * @return string
     */
    public function getSearchQuery(): string
    {
        /** @var Search_Helper_Search $searchHelper */
        $searchHelper = App::getSingleton('helper', 'search');

        return $searchHelper->getSearchQuery();
    }
}
