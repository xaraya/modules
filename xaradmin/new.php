<?php
/**
 * File: $Id:
 *
 * Standard function to install a text
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
 * install text
 * @param  $ 'tid' the id of the text to be installed
 * @param  $ 'confirm' confirm that this text should be installed
 */
function bible_admin_new($args)
{
    extract($args);

    if (!xarVarFetch('tid', 'int:0', $tid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return', 'str:1:', $return, xarServerGetVar('HTTP_REFERER'), XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (!empty($objectid)) $tid = $objectid;
    if (empty($return)) $return = '';

    // get text data
    $text = xarModAPIFunc(
        'bible', 'user', 'get', array('tid' => $tid, 'state' => 'all')
    );
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddBible', 1, 'Text', "$text[sname]:$tid")) {
        return;
    }

    // Check for confirmation.
    if (empty($confirm)) {

        // initialize template data
        $data = xarModAPIFunc('bible', 'admin', 'menu');

        // Specify for which item you want confirmation
        $data['tid'] = $tid;

        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Are you sure you want to install this text?  It may take several seconds.');
        $data['textid'] = xarML('Text ID');
        $data['snamelabel'] = xarML('Short Name');
        $data['confirmbutton'] = xarML('Confirm');

        $data['snamevalue'] = xarVarPrepForDisplay($text['sname']);
        $data['authid'] = xarSecGenAuthKey();
        $data['return'] = $return;

        return $data;
    }

    if (!xarSecConfirmAuthKey()) return;

    // call API function to do the deleting
    if (!xarModAPIFunc('bible', 'admin', 'install', array('tid' => $tid))) return;

    // now that we're done, set new state
    if (!xarModAPIFunc('bible', 'admin', 'setstate',
        array('tid' => $tid, 'newstate' => 2))) return;

    // now send to the page where we create the index
    if (empty($return)) $return = xarModURL('bible', 'admin', 'view');
    xarResponseRedirect($return);

    return true;
}

?>
