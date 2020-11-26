<?php

declare(strict_types=1);

/**
 * Class Fpc_Observer_SaveCache
 */
class Fpc_Observer_SaveCache extends Core_Observer_Abstract
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
        /** @var Core_Page $page */
        $page = $object->getData('object');
        /** @var Fpc_Helper_Cache $helper */
        $helper = App::getObject('helper', 'fpc_cache');

        if (!$helper->canCache($page)) {
            return;
        }

        $helper->saveCache($page);
    }
}
