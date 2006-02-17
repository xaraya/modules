<?php
/**
 * Standard function to delete an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author Michel V.
 */
/**
 * delete item
 * This is a standard function that is called whenever an administrator
 * wishes to delete a current module item.  Note that this function is
 * the equivalent of both of the modify() and update() functions above as
 * it both creates a form and processes its output.  This is fine for
 * simpler functions, but for more complex operations such as creation and
 * modification it is generally easier to separate them into separate
 * functions.  There is no requirement in the Xaraya MDG to do one or the
 * other, so either or both can be used as seen appropriate by the module
 * developer
 *
 * @param  $ 'personid' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function sigmapersonnel_admin_delete($args)
{
    extract($args);
    if (!xarVarFetch('personid', 'int:1:', $personid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $personid = $objectid;
    }
    // Get item
    $item = xarModAPIFunc('sigmapersonnel',
        'user',
        'get',
        array('personid' => $personid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $persstatus = $item['persstatus'];
    if (!xarSecurityCheck('DeleteSIGMAPersonnel', 1, 'PersonnelItem', "$personid:All:$persstatus")) {
        return;
    }
    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet
        $data = xarModAPIFunc('sigmapersonnel', 'admin', 'menu');
        // Specify for which item you want confirmation
        $data['personid'] = $personid;
        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Confirm deleting this person? Maybe better change his/her status?');
        $data['itemid'] = xarML('Item ID');
        $data['firstnamelabel'] = xarML('First Name');
        $data['firstnamevalue'] = xarVarPrepForDisplay($item['firstname']);
        $data['lastnamelabel'] = xarML('Last Name');
        $data['lastnamevalue'] = xarVarPrepForDisplay($item['lastname']);
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        // Return the template variables defined in this function
        return $data;
    }
    // If we get here it means that the user has confirmed the action
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('sigmapersonnel',
            'admin',
            'delete',
            array('personid' => $personid))) {
        return; // throw back
    }
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('sigmapersonnel', 'admin', 'view'));
    // Return
    return true;
}

?>
