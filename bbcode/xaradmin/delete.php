<?php
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