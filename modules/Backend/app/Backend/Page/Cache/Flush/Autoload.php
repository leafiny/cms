<?php

declare(strict_types=1);

/**
 * Class Backend_Page_Cache_Flush_Autoload
 */
class Backend_Page_Cache_Flush_Autoload extends Backend_Page_Admin_Page_Abstract
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

        $directory = App::getClassSymlinksDir();

        try {
            if (is_dir($directory)) {
                $file->rmdir($directory);
            }
            $this->setSuccessMessage(App::translate('Class symlinks have been flushed.'));
            App::dispatchEvent(
                'flush_cache_success',
                ['type' => 'autoload']
            );
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
            App::dispatchEvent(
                'flush_cache_error',
                [
                    'type'  => 'autoload',
                    'error' => $throwable->getMessage(),
                ]
            );
        }

        $this->redirect($this->getRefererUrl(), true);
    }
}
