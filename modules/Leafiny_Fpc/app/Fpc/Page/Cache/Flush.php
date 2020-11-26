<?php

declare(strict_types=1);

/**
 * Class Fpc_Page_Cache_Flush
 */
class Fpc_Page_Cache_Flush extends Backend_Page_Admin_Page_Abstract
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

        $directory = $helper->getCacheDir() . Fpc_Helper_Cache::CACHE_FPC_DIRECTORY;

        try {
            $files = 0;
            if (is_dir($directory)) {
                $files = $file->rmdir($directory);
            }
            $this->setSuccessMessage(
                App::translate('The cache has been flushed.') .
                ' ' . $files . ' ' . App::translate('file(s) deleted.')
            );
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
        }

        $this->redirect($this->getRefererUrl(), true);
    }
}
