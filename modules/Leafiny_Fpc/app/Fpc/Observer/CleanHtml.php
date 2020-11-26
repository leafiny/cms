<?php

declare(strict_types=1);

/**
 * Class Fpc_Observer_CleanHtml
 */
class Fpc_Observer_CleanHtml extends Core_Observer_Abstract
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

        if ($page->getCustom('fpc_is_cached')) {
            return;
        }

        $render = $page->getData('render');

        $page->setData('render', preg_replace('/<!--(.|\s)*?-->/', '', $render));
    }
}
