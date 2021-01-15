<?php

declare(strict_types=1);

/**
 * Class Rewrite_Observer_Rewrite
 */
class Rewrite_Observer_Rewrite extends Core_Event implements Core_Interface_Event
{
    /**
     * Execute
     *
     * @param Core_Page|Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $extract */
        $extract = $object->getData('extract');
        /** @var Rewrite_Model_Rewrite $rewrite */
        $rewrite = App::getObject('model', 'rewrite');

        $identifier = $extract->getData('identifier');
        if (!$identifier) {
            return;
        }

        $params = '';
        if (preg_match('/\\?(?P<params>.*)/', $identifier, $matches)) {
            $params = '?' . $matches['params'];
            $identifier = preg_replace('/\\?.*/', '', $identifier);
        }

        try {
            $identifier = $rewrite->getBySource($identifier)->getData('target_identifier');
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        if ($identifier) {
            $extract->setData('identifier', $identifier . $params);
        }
    }
}
