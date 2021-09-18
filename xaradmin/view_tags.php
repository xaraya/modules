<?php
/**
 * Karma Module
 *
 * @package modules
 * @subpackage karma
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * View items of the karma_tags object
 *
 */
    function karma_admin_view_tags($args)
    {
        if (!xarSecurity::check('EditKarma')) {
            return;
        }

        $data['object'] = xarMod::apiFunc('dynamicdata', 'user', 'getobjectlist', ['name' => 'karma_tags']);
        $data['object']->getItems();
        return $data;
    }
