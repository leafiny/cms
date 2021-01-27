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
 * Class Fpc_Page_Cache_Flush_Key
 */
class Fpc_Page_Cache_Flush_Key extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $post = $this->getPost();

        if (!$post->hasData('cache_key')) {
            $this->redirect($this->getRefererUrl(), true);
        }

        $cacheKey = $post->getData('cache_key') ?: 'default';

        /** @var Fpc_Helper_Cache $helper */
        $helper = App::getObject('helper', 'fpc_cache');

        $result = $helper->flushCache($cacheKey);

        if ($result) {
            $this->setSuccessMessage(App::translate('Cache flushed for key:') . ' ' . $cacheKey);
        } else {
            $this->setWarningMessage(
                App::translate('There is no cache for this key or it has already been flushed.')
            );
        }

        App::dispatchEvent(
            'fpc_flush_cache_key_after',
            [
                'result'    => $result,
                'cache_key' => $cacheKey,
            ]
        );

        $this->redirect($this->getRefererUrl(), true);
    }
}
