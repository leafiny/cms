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
 * Class Search_Page_Backend_Refresh
 */
class Search_Page_Backend_Refresh extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        /** @var Search_Helper_Search $searchHelper */
        $searchHelper = App::getSingleton('helper', 'search');

        try {
            $searchHelper->getEngine()->refreshAll();
            $this->setSuccessMessage(App::translate('Data has been updated.'));
            App::dispatchEvent(
                'flush_cache_success',
                ['type' => 'search']
            );
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
            App::dispatchEvent(
                'flush_cache_error',
                [
                    'type'  => 'search',
                    'error' => $throwable->getMessage(),
                ]
            );
        }

        $this->redirect($this->getRefererUrl());
    }
}
