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
 * Class Rewrite_Page_Backend_Refresh
 */
class Rewrite_Page_Backend_Refresh extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        /** @var Rewrite_Model_Rewrite $rewrite */
        $rewrite = App::getObject('model', 'rewrite');

        try {
            if ($rewrite->refreshAll()) {
                $this->setSuccessMessage(App::translate('URL rewrites have been refreshed'));
            }
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
        }

        $this->redirect($this->getRefererUrl());
    }
}
