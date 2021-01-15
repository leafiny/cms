<?php

declare(strict_types=1);

/**
 * Class Backend_Observer_KeyWarning
 */
class Backend_Observer_KeyWarning extends Core_Event implements Core_Interface_Event
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Backend_Page_Admin_Page_Abstract $page */
        $page = $object->getData('object');

        if ($page->getObjectIdentifier() === '/admin/*/' && $page->getObjectKey() === 'leafiny') {
            $page->setWarningMessage(
                App::translate('Backend key is set to "leafiny". Please modify for more security.')
            );
        }
    }
}
