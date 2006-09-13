<?php
/**
 * Delete an id
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Delete an ID
 * 
 * @param $rid ID
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_deleteid()
{
    // Get parameters
    if (!xarVarFetch('eid', 'id', $eid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirmation','str:1:',$confirmation,'',XARVAR_NOT_REQUIRED)) return;
    
    extract($args);

    if (!empty($obid)) {
        $rid = $obid;
    } 

    // The user API function is called.
    $data = xarModAPIFunc('release', 'user', 'getid',
                          array('eid' => $eid));

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

    if (!xarModAPIFunc('release', 'admin', 'deleteid',
                        array('eid' => $eid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('release', 'admin', 'viewids'));

    // Return
    return true;
}

?>