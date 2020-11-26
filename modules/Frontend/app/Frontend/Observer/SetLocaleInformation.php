<?php

/**
 * Class Frontend_Observer_SetLocaleInformation
 */
class Frontend_Observer_SetLocaleInformation extends Core_Observer_Abstract
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

        if ($page->getCustom('locale')) {
            setlocale(LC_ALL, $page->getCustom('locale'));
        }
    }
}
