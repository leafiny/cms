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
 * Class Backend_Page_Account_Form
 */
class Backend_Page_Account_Form extends Backend_Page_Admin_Form
{
    /**
     * Retrieve POST AND REQUEST params
     *
     * @param string[] $types
     *
     * @return Leafiny_Object
     */
    public function getParams(array $types = ['get', 'post']): Leafiny_Object
    {
        $params = parent::getParams($types);

        /** @var Backend_Model_Admin_User $user */
        $user = App::getObject('model', 'admin_user');
        $params->setData('id', $user->getCurrentUserId());

        return $params;
    }
}
