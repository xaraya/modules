<?php
/**
 * delete item
 * @param 'id' the id of the item to be deleted
 * @param 'confirm' confirmation that this item can be deleted
 */
function sitecloud_admin_delete()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('id','int:1:',$id)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$id,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;
    // Security Check
	if(!xarSecurityCheck('Deletesitecloud')) return;
    // The user API function is called
    $link = xarModAPIFunc('sitecloud',
                          'user',
                          'get',
                          array('id' => $id));

    if ($link == false) return; 

    // Check for confirmation.
    if (empty($confirm)) {
    $link['submitlabel'] = xarML('Submit');
    $link['authid'] = xarSecGenAuthKey();
    return $link;
    }
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // The API function is called
    if (!xarModAPIFunc('sitecloud',
                       'admin',
                       'delete',
                       array('id' => $id))) return; 
    xarResponseRedirect(xarModURL('sitecloud', 'admin', 'view'));
    // Return
    return true;
}
?>
