<?php
/**
 * Delete a call from the database
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * delete a call
 *
 * @param  $ 'callid' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function maxercalls_admin_deletecall($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('callid',   'int:1:', $callid)) return;
    if (!xarVarFetch('objectid', 'id',     $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    //Check for external input
    if (!empty($objectid)) {
        $callid = $objectid;
    }
    // The user API function is called.  This takes the item ID which we
    // obtained from the input and gets us the information on the appropriate
    // item.  If the item does not exist we post an appropriate message and
    // return
    $item = xarModAPIFunc('maxercalls',
        'user',
        'get',
        array('callid' => $callid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('DeleteMaxercalls', 1, 'Item', "$callid:All:$item[enteruid]")) {
        return;
    }
    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet
        $data = xarModAPIFunc('maxercalls', 'admin', 'menu');
        // Specify for which item you want confirmation
        $data['callid'] = $callid;
        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Confirm deleting this item?');
        $data['itemid'] = xarML('Item ID');
        $data['calldatelabel'] = xarVarPrepForDisplay(xarML('Date of call'));
        $data['calltimelabel'] = xarVarPrepForDisplay(xarML('Time of call'));
        $data['entertslabel'] = xarVarPrepForDisplay(xarML('Entered on'));
        $data['ownerlabel'] = xarVarPrepForDisplay(xarML('Owner of maxer'));
        $data['enteredbylabel'] = xarVarPrepForDisplay(xarML('Entered by'));
        $data['remarkslabel'] = xarVarPrepForDisplay(xarML('Remarks'));
        $data['calldate'] = xarVarPrepForDisplay($item['calldate']);
        $data['calltime'] = xarVarPrepForDisplay($item['calltime']);
        $data['enteruid'] = $item['enteruid'];
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        // Return the template variables defined in this function
        return $data;
    }
    // Delete the call
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('maxercalls',
            'admin',
            'deletecall',
            array('callid' => $callid))) {
        return; // throw back
    }
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('maxercalls', 'admin', 'view'));
    // Return
    return true;
}

?>
