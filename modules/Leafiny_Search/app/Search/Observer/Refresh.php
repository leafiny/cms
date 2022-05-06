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
 * Class Search_Observer_Refresh
 */
class Search_Observer_Refresh extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Core_Model $toRefresh */
        $objectId = $object->getData('object_id');
        /** @var string $identifier */
        $identifier = $object->getData('identifier');

        if (!$identifier || !$objectId) {
            return;
        }

        /** @var Search_Helper_Search $searchHelper */
        $searchHelper = App::getSingleton('helper', 'search');

        try {
            $searchHelper->getEngine()->refresh($identifier, (int)$objectId);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }
}
