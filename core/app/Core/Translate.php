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
 * Class Core_Translate
 */
class Core_Translate extends Leafiny_Translate
{
    /**
     * Process Config
     *
     * @return void
     * @throws Exception
     */
    public function process(): void
    {
        $files = [];

        $modules = $this->getHelper()->getModules();
        foreach ($modules as $module) {
            $file = $this->getHelper()->getTranslateDir($module) . App::getLanguage() . '.csv';
            if (is_file($file)) {
                $files[] = $file;
            }
        }

        $this->setFiles($files);
        $this->load();
    }

    /**
     * Retrieve Helper
     *
     * @return Core_Helper
     */
    public function getHelper(): Core_Helper
    {
        return App::getSingleton('helper');
    }
}
