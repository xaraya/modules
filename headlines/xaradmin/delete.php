<?
/**
 * delete item
 * @param 'hid' the id of the item to be deleted
 * @param 'confirmation' confirmation that this item can be deleted
 */
function headlines_admin_delete($args)
{
    // Get parameters from whatever input we need
    list($hid,
         $obid,
         $confirmation) = xarVarCleanFromInput('hid',
                                               'obid',
                                               'confirmation');
    extract($args);

     if (!empty($obid)) {
         $hid = $obid;
     }

    // Security Check
	if(!xarSecurityCheck('DeleteHeadlines')) return;

    // The user API function is called
    $link = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($link == false) return; 

    // Check for confirmation.
    if (empty($confirmation)) {
    $link['submitlabel'] = xarML('Submit');
    $link['authid'] = xarSecGenAuthKey();

    return $link;

    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // The API function is called
    if (!xarModAPIFunc('headlines',
                       'admin',
                       'delete',
                       array('hid' => $hid))) return; 

    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));

    // Return
    return true;
}
?>