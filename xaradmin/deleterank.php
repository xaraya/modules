<?php
/**
 * Standard function to delete an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage userpoints
 * @author Userpoints module development team
 */
/**
 * delete a rank
 *
 * @param id $id the id of the item to be deleted
 * @param str $confirm confirm that this item can be deleted
 */
function userpoints_admin_deleterank($args)
{
    extract($args);

    if (!xarVarFetch('id', 'int:1:', $id)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $id = $objectid;
    }
    // The user API function is called.
    $item = xarModAPIFunc('userpoints',
                          'user',
                          'getrank',
        array('id' => $id));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('DeleteUserpointsRank', 1, 'Rank', "$item[rankname]:$id")) {
        return;
    }
    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user
        $data['id'] = $id;
        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Do you really want to delete this rank?');
        $data['itemid'] = xarML('Rank ID');
        $data['ranknamelabel'] = xarML('Rank Name');
        $data['ranknamevalue'] = xarVarPrepForDisplay($item['rankname']);
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        // Return the template variables defined in this function
        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    // The return value of the function is checked here
    if (!xarModAPIFunc('userpoints',
                       'admin',
                       'deleterank',
            array('id' => $id))) {
        return; // throw back
    }
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('userpoints', 'admin', 'viewrank'));
    // Return
    return true;
}
?>
