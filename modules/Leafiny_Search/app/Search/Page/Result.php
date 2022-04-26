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
 * Class Search_Page_Result
 */
class Search_Page_Result extends Core_Page
{
    public function action(): void
    {
        parent::action();

        /** @var Search_Helper_Search $searchHelper */
        $searchHelper = App::getSingleton('helper', 'search');
        if (!$searchHelper->getSearchQuery()) {
            $this->redirect();
        }
    }

    /**
     * Retrieve search results
     *
     * @return string[]
     * @throws Exception
     */
    public function getSearchResult(): array
    {
        /** @var Search_Helper_Search $searchHelper */
        $searchHelper = App::getSingleton('helper', 'search');
        $engine = $searchHelper->getEngine();

        $results = [];

        foreach ($engine->getObjectTypes() as $type => $options) {
            $search = $engine->search($searchHelper->getSearchQuery(), $type, App::getLanguage());
            if (empty($search)) {
                continue;
            }

            $results[$options['position'] ?? 0] = new Leafiny_Object(
                [
                    'block'    => $options['block'] ?? '',
                    'response' => $search,
                ]
            );
        }

        ksort($results);

        return $results;
    }
}
