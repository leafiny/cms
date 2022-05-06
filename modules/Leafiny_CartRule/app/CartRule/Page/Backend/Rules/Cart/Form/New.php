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
 * Class CartRule_Page_Backend_Rules_Cart_Form_New
 */
class CartRule_Page_Backend_Rules_Cart_Form_New extends CartRule_Page_Backend_Rules_Cart_Form
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
