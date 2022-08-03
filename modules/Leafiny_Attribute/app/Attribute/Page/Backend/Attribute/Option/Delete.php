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

class Attribute_Page_Backend_Attribute_Option_Delete extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        $params = $this->getParams();
        if ($params->getData('option_id')) {
            /** @var Attribute_Model_Attribute $modelAttribute */
            $modelAttribute = App::getSingleton('model', 'attribute');

            try {
                $modelAttribute->removeOption((int)$params->getData('option_id'));
            } catch (Throwable $throwable) {
                App::log($throwable, Core_Interface_Log::ERR);
            }
        }

        $this->redirect($this->getRefererUrl() . '#section-options');
    }
}
