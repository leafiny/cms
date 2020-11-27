<?php

declare(strict_types=1);

/**
 * Class Backend_Page_Cache_Flush_Template
 */
class Backend_Page_Cache_Flush_Template extends Backend_Page_Admin_Page_Abstract
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

        $directory = $helper->getCacheDir() . Core_Template_Abstract::CACHE_TWIG_DIRECTORY;

        try {
            $files = 0;
            if (is_dir($directory)) {
                $files = $file->rmdir($directory);
            }
            $this->setSuccessMessage(
                App::translate('The cache has been flushed.') .
                ' ' . $files . ' ' . App::translate('file(s) deleted.')
            );
            App::dispatchEvent(
                'flush_cache_success',
                ['type' => 'template']
            );
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
            App::dispatchEvent(
                'flush_cache_error',
                [
                    'type'  => 'template',
                    'error' => $throwable->getMessage(),
                ]
            );
        }

        $this->redirect($this->getRefererUrl(), true);
    }
}
