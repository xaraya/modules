<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Modify an item of the realms object
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function realms_admin_modify()
{
    if (!xarSecurity::check('EditRealms')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'realms_realms', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'realms';
    $data['authid'] = xarSec::genAuthKey('realms');

    if ($data['confirm']) {
        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('realms', 'admin', 'modify', $data);
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

            // Jump to the next page
            xarController::redirect(xarController::URL('realms', 'admin', 'view'));
            return true;
        }
    }

    return $data;
}
