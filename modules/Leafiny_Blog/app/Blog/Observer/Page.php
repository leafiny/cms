<?php

declare(strict_types=1);

/**
 * Class Rewrite_Observer_Rewrite
 */
class Blog_Observer_Page extends Core_Observer_Abstract
{
    /**
     * Execute
     *
     * @param Core_Page|Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $extract */
        $extract = $object->getData('extract');

        $identifier = $extract->getData('identifier');
        if (!$identifier) {
            return;
        }

        $pageParam = Blog_Helper_Data::URL_PARAM_PAGE;

        if (!preg_match('/^(?P<key>.*)' . $pageParam . '(?P<page>[0-9]+)/', $identifier, $matches)) {
            return;
        }

        $new = preg_replace('/' . $pageParam . $matches['page'] . '/', '', $identifier);

        if (preg_match('/\\?(?P<params>.*)/', $identifier)) {
            $new .= '&p=' . $matches['page'];
        } else {
            $new .= '?p=' . $matches['page'];
        }

        $extract->setData('identifier', $new);
    }
}
