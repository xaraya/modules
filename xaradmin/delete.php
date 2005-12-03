<?php
/**
 * File: $Id:
 *
 * Standard function to delete a text
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
/**
 * delete text
 * developer
 *
 * @param  $ 'tid' the id of the text to be deleted
 * @param  $ 'confirm' confirm that this text can be deleted
 */
function bible_admin_delete($args)
{
    extract($args);

    if (!xarVarFetch('tid', 'int:1:', $tid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return', 'str:1:', $return, xarServerGetVar('HTTP_REFERER'), XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (!empty($objectid)) $tid = $objectid;
    if (empty($return)) $return = '';

    $text = xarModAPIFunc('bible', 'user', 'get',
                          array('tid' => $tid, 'state' => 'all'));

    // Check for exceptions
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // security check
    if (!xarSecurityCheck('DeleteBible', 1, 'Text', "$text[sname]:$tid")) {
        return;
    }

    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user
        $data = xarModAPIFunc('bible', 'admin', 'menu');

        // Specify for which item you want confirmation
        $data['tid'] = $tid;

        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Are you sure you want to delete this text?');
        $data['textid'] = xarML('Text ID');
        $data['snamelabel'] = xarML('Short Name');
        $data['snamevalue'] = xarVarPrepForDisplay($text['sname']);
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        $data['return'] = $return;
        // Return the template variables defined in this function
        return $data;
    }

    if (!xarSecConfirmAuthKey()) return;

    // call API function to do the deleting
    if (!xarModAPIFunc('bible',
            'admin',
            'delete',
            array('tid' => $tid))) {
        return; // throw back
    }

    // now send to the page where we create the index
    if (empty($return)) $return = xarModURL('bible', 'admin', 'view');
    xarResponseRedirect($return);

    // Return
    return true;
}

?>
