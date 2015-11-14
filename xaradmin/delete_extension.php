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
    if (!xarSecurityCheck('ManageRelease')) return;

    if (!xarVarFetch('name',       'str:1',     $name,            'release_extensions',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',       $data['itemid'] , '' ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'checkbox',  $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'release';
    $data['authid'] = xarSecGenAuthKey('release');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Delete the item
        $item = $data['object']->deleteItem();
            
        // Jump to the next page
        xarController::redirect(xarModURL('release','admin','view_extensions'));
        return true;
    }
    return $data;
}
/*
    // Get parameters
    if (!xarVarFetch('eid', 'id', $eid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirmation','str:1:',$confirmation,'',XARVAR_NOT_REQUIRED)) return;
    
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
    if(!xarSecurityCheck('ManageRelease')) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!xarMod::apiFunc('release', 'admin', 'deleteid',
                        array('eid' => $eid,'rid'=>$rid, 'regname'=>$regname))) return;

    // Redirect
    xarController::redirect(xarModURL('release', 'admin', 'viewids'));

    // Return
    return true;
}
*/
?>