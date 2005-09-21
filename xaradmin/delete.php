<?php
/**
 * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/
function smilies_admin_delete()
{
    // Security Check
    if(!xarSecurityCheck('DeleteSmilies')) return;
    if (!xarVarFetch('sid','int',$sid)) return;
    if (!xarVarFetch('confirmation','id',$confirmation, '',XARVAR_NOT_REQUIRED)) return;

    // The user API function is called.
    $data = xarModAPIFunc('smilies',
                          'user',
                          'get',
                          array('sid' => $sid));

    if ($data == false) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Remove User From Group.
    if (!xarModAPIFunc('smilies',
                       'admin',
                       'delete', 
                        array('sid' => $sid))) return;
    // Redirect
    xarResponseRedirect(xarModURL('smilies', 'admin', 'view'));
    // Return
    return true;
}
?>