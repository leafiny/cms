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
 * Class Backend_Page_Admin_Form_New
 */
class Backend_Page_Admin_Form_New extends Backend_Page_Admin_Form
{
    /**
     * Pre process
     * @throws Exception
     */
    public function preProcess(): void
    {
        parent::preProcess();

        $forward = $this->getPathName($this->getObjectIdentifier()) . 'edit/';

        $title = $this->getCustom('title');

        $this->forward($forward);

        $this->setCustom('title', $title);
    }
}
