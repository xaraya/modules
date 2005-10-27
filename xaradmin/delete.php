<?php
/**
 * Xaraya BBCode
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BBCode Module
 * @link http://xaraya.com/index.php/release/778.html
 * @author John Cox
*/
function bbcode_admin_delete()
{
    // Security Check
    if(!xarSecurityCheck('EditBBCode')) return;
    if (!xarVarFetch('id','int',$id)) return;
    if (!xarVarFetch('confirmation','id',$confirmation, '',XARVAR_NOT_REQUIRED)) return;

    // The user API function is called.
    $data = xarModAPIFunc('bbcode',
                          'user',
                          'get',
                          array('id' => $id));

    if ($data == false) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        $data['submitlabel'] = xarML('Submit');
        return $data;
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Remove User From Group.
    if (!xarModAPIFunc('bbcode',
                       'admin',
                       'delete', 
                        array('id' => $id))) return;
    // Redirect
    xarResponseRedirect(xarModURL('bbcode', 'admin', 'view'));
    // Return
    return true;
}
?>