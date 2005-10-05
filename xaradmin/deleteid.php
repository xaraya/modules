<?php
/**
 * Delete an id
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 */
/**
 * Delete and ID
 * 
 * @param $rid ID
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_deleteid()
{
    // Get parameters
    if (!xarVarFetch('rid', 'id', $rid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirmation','str:1:',$confirmation,'',XARVAR_NOT_REQUIRED)) return;
    
    extract($args);

    if (!empty($obid)) {
        $rid = $obid;
    } 

    // The user API function is called.
    $data = xarModAPIFunc('release',
                          'user',
                          'getid',
                          array('rid' => $rid));

    if ($data == false) return;

    // Security Check
    if(!xarSecurityCheck('DeleteRelease')) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('release',
                       'admin',
                       'deleteid', 
                        array('rid' => $rid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('release', 'admin', 'viewids'));

    // Return
    return true;
}

?>