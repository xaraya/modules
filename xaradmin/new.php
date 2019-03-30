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
 * Create a new item of the karma object
 *
 */
    function karma_admin_new()
    {
        if (!xarSecurityCheck('AddKarma')) return;

        $data['object'] = xarMod::apiFunc('dynamicdata','user','getobjectlist', array('name' => 'karma'));
        $data['tplmodule'] = 'karma';
        return $data;
    }

?>