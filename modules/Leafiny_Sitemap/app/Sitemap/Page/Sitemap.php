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
 * Class Sitemap_Page_Sitemap
 */
class Sitemap_Page_Sitemap extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        /** @var Sitemap_Model_Sitemap $sitemapModel */
        $sitemapModel = App::getSingleton('model', 'sitemap');
        $urls = $sitemapModel->getUrls();

        App::dispatchEvent('sitemap_urls', ['urls' => $urls]);

        $language = $this->getObjectKey() ?: App::getLanguage();
        if (!$urls->getData($language)) {
            $this->error(true);
            return;
        }

        $this->setCustom('urls', $urls->getData($language));

        header('Content-Type: application/xml; charset=utf-8');
    }
}
