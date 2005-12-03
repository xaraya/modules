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

    // get HTTP vars
    if (!xarVarFetch('tid', 'int:1:', $tid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return', 'str:1:', $return, xarServerGetVar('HTTP_REFERER'), XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (!empty($objectid)) $tid = $objectid;
    if (empty($return)) $return = '';

    // get text
    $text = xarModAPIFunc(
        'bible', 'user', 'get', array('tid' => $tid, 'state' => 'all')
    );
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('DeleteBible', 1, 'Text', "$text[sname]:$tid")) return;

    // Check for confirmation.
    if (empty($confirm)) {

        // initialize template vars
        $data = xarModAPIFunc('bible', 'admin', 'menu');

        // set template vars
        $data['tid'] = $tid;
        $data['text'] = &$text;
        $data['authid'] = xarSecGenAuthKey();
        $data['return'] = $return;

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // call API function to do the deleting
    if (!xarModAPIFunc('bible', 'admin', 'delete', array('tid' => $tid))) return;

    // set status messagee and send to the page where we create the index
    xarSessionSetVar('statusmsg', xarML('Text successfully deleted!'));
    if (empty($return)) $return = xarModURL('bible', 'admin', 'view');
    xarResponseRedirect($return);

    // Return
    return true;
}

?>
