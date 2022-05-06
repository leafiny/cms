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
 * Interface Search_Interface_Engine
 */
interface Search_Interface_Engine
{
    /**
     * Search by query
     *
     * @param string      $query
     * @param string      $objectType
     * @param string|null $language
     *
     * @return mixed[]
     */
    public function search(string $query, string $objectType, ?string $language = null): array;

    /**
     * Refresh data by object type
     *
     * @param string   $objectType
     * @param int|null $objectId
     *
     * @return bool
     */
    public function refresh(string $objectType, ?int $objectId = null): bool;

    /**
     * Delete data by object type
     *
     * @param string   $objectType
     * @param int|null $objectId
     *
     * @return bool
     */
    public function remove(string $objectType, ?int $objectId = null): bool;

    /**
     * Refresh data for all object types
     *
     * @return void
     */
    public function refreshAll(): void;
}
