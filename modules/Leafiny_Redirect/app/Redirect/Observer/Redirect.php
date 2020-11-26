<?php

declare(strict_types=1);

/**
 * Class Redirect_Observer_Redirect
 */
class Redirect_Observer_Redirect extends Core_Observer_Abstract
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
        /** @var Redirect_Model_Redirect $redirect */
        $redirect = App::getObject('model', 'redirect');

        $identifier = $extract->getData('identifier');
        if (!$identifier) {
            return;
        }

        try {
            $target = $redirect->getBySource($identifier);
            if ($target->hasData()) {
                /** @var Core_Page $page */
                $page = App::getObject('page');
                $page->redirect(
                    $page->getUrl($target->getData('target_identifier')),
                    (int)$target->getData('redirect_type')
                );
            }
        } catch (Exception $exception) {
            /** @var Log_Model_File $log */
            $log = App::getObject('model', 'log_file');
            $log->add($exception->getMessage());
        }
    }
}
