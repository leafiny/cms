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
 * Class Search_Helper_Search
 */
class Search_Helper_Search extends Core_Helper
{
    /**
     * Retrieve Search Query
     *
     * @return string
     */
    public function getSearchQuery(): string
    {
        $query = $_GET['q'] ?? '';
        $query = strip_tags((string)$query);

        if (strlen($query) < $this->getMinQueryLength()) {
            $query = '';
        }

        return strtolower($query);
    }

    /**
     * Use the nearest words
     *
     * @return bool
     */
    public function canUseNearestWords(): bool
    {
        return (bool)$this->getCustom('nearest_words');
    }

    /**
     * Retrieve all object types with options
     *
     * @return mixed[]
     */
    public function getObjectTypes(): array
    {
        return $this->getCustom('entity') ?: [];
    }

    /**
     * Retrieve engine
     *
     * @return Search_Interface_Engine
     */
    public function getEngine(): Search_Interface_Engine
    {
        return App::getObject('model', $this->getCustom('engine') ?? null);
    }

    /**
     * Retrieve min query length
     *
     * @return int
     */
    public function getMinQueryLength(): int
    {
        $length = $this->getCustom('min_query_length');
        if (!$length) {
            return 3;
        }

        return (int)$length;
    }
}
