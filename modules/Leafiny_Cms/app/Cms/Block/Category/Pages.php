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
 * Class Cms_Block_Category_Pages
 */
class Cms_Block_Category_Pages extends Core_Block
{
    /**
     * Retrieve pages
     *
     * @param int $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getPages(int $categoryId): array
    {
        if ($this->getCustom('pages')) {
            return $this->getCustom('pages');
        }

        /** @var Cms_Helper_Cms_Page $helper */
        $helper = App::getSingleton('helper', 'cms_page');

        return $helper->getCategoryPages($categoryId);
    }
}
