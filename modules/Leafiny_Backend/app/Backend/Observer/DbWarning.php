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
 * Class Backend_Observer_DbWarning
 */
class Backend_Observer_DbWarning extends Core_Event implements Core_Interface_Event
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
        /** @var Core_Model $model */
        $model = App::getObject('model');

        if ($model->isDbNoWriting() && !empty($_POST)) {
            /** @var Backend_Page_Admin_Page_Abstract $page */
            $page = $object->getData('object');

            $page->setWarningMessage(
                App::translate('Database writing statements are disabled')
            );
        }
    }
}
