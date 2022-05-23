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
 * Class Cms_Block_Category_Blocks
 */
class Cms_Block_Category_Blocks extends Core_Block
{
    /**
     * Retrieve blocks
     *
     * @param int $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getBlocks(int $categoryId): array
    {
        if ($this->getCustom('blocks')) {
            return $this->getCustom('blocks');
        }

        /** @var Cms_Helper_Cms_Block $helper */
        $helper = App::getSingleton('helper', 'cms_block');

        return $helper->getCategoryBlocks($categoryId);
    }
}
