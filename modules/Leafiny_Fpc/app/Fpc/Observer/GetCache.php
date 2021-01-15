<?php

declare(strict_types=1);

/**
 * Class Fpc_Observer_GetCache
 */
class Fpc_Observer_GetCache extends Core_Event implements Core_Interface_Event
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

        try {
            $template = $helper->getCacheFile($page);

            if ($template) {
                $page->setCustom('fpc_is_cached', true);
                $page->setCustom('template', $template);
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }
}
