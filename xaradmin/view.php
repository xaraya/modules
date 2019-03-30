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
 * View items of the karma object
 *
 */
    function karma_admin_view($args)
    {
        if (!xarSecurityCheck('EditKarma')) return;

        $data['object'] = xarMod::apiFunc('dynamicdata','user','getobjectlist', array('name' => 'karma'));
        $data['object']->getItems();
        return $data;
    }
?>