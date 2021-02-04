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
 * Class Backend_Page_Cache_Flush_Config
 */
class Backend_Page_Cache_Flush_Config extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        /** @var Core_Helper_File $file */
        $file = App::getObject('helper_file');
        /** @var Core_Helper $helper */
        $helper = App::getObject('helper');

        $directory = $helper->getCacheDir() . Core_Config::CACHE_CONFIG_DIRECTORY;

        try {
            if (is_dir($directory)) {
                $file->rmdir($directory);
            }
            $this->setSuccessMessage(App::translate('The cache has been flushed.'));
            App::dispatchEvent(
                'flush_cache_success',
                ['type' => 'config']
            );
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
            App::dispatchEvent(
                'flush_cache_error',
                [
                    'type'  => 'config',
                    'error' => $throwable->getMessage(),
                ]
            );
        }

        $this->redirect($this->getRefererUrl());
    }
}
