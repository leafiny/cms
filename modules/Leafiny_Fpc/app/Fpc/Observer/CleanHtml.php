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
 * Class Fpc_Observer_CleanHtml
 */
class Fpc_Observer_CleanHtml extends Core_Event implements Core_Interface_Event
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
