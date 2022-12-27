<?php
/**
 * Delete an extension
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Delete an extension
 *
 * @param $rid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_delete_extension($args)
{
    if (!xarSecurity::check('ManageRelease')) {
        return;
    }

    if (!xarVar::fetch('name', 'str:1', $name, 'release_extensions', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'checkbox', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'release';
    $data['authid'] = xarSec::genAuthKey('release');

    if ($data['confirm']) {
        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Delete the item
        $item = $data['object']->deleteItem();

        // Jump to the next page
        xarController::redirect(xarController::URL('release', 'admin', 'view_extensions'));
        return true;
    }
    return $data;
}
/*
    // Get parameters
    if (!xarVar::fetch('eid', 'id', $eid)) return;
    if (!xarVar::fetch('obid', 'str:1:', $obid, '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('confirmation','str:1:',$confirmation,'',xarVar::NOT_REQUIRED)) return;

    extract($args);

    if (!empty($obid)) {
        $rid = $obid;
    }

    // The user API function is called.
    $data = xarMod::apiFunc('release', 'user', 'getid',
                          array('eid' => $eid));

    if ($data == false) return;
    $rid = $data['rid'];
    $regname = $data['regname'];
    // Security Check
    if(!xarSecurity::check('ManageRelease')) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSec::genAuthKey();
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSec::confirmAuthKey()) return;

    if (!xarMod::apiFunc('release', 'admin', 'deleteid',
                        array('eid' => $eid,'rid'=>$rid, 'regname'=>$regname))) return;

    // Redirect
    xarController::redirect(xarController::URL('release', 'admin', 'viewids'));

    // Return
    return true;
}
*/
