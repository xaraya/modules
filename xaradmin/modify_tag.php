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
 * Modify an item of the tags object
 *
 */
    
function karma_admin_modify()
{
    if (!xarSecurity::check('EditKarma')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'karma_tags', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'checkbox', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'karma';
    $data['authid'] = xarSec::genAuthKey('karma');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('karma', 'admin', 'modify', $data);
        } else {
            // Good data: create the item
            $itemid = xarMod::apiFunc('karma', 'admin', 'modify_tag', array('itemid' => $data['itemid']));
            // Jump to the next page
            xarController::redirect(xarController::URL('karma', 'admin', 'view'));
            return true;
        }
    }
    return $data;
}
