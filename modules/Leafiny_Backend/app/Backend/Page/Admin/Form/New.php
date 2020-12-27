<?php

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
