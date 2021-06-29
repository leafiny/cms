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
 * Class Redirect_Observer_Redirect
 */
class Redirect_Observer_Redirect extends Core_Observer implements Core_Interface_Observer
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
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }
}
