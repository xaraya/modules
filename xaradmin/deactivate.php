<?php
/**
 * File: $Id:
 *
 * Deactivate a text
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
 * deactivate text
 * @param  $ 'tid' the id of the text to be installed
 */
function bible_admin_deactivate($args)
{
    extract($args);

    if (!xarVarFetch('tid', 'int:1:', $tid)) return;
    if (!xarVarFetch('return', 'str:1:', $return, xarServerGetVar('HTTP_REFERER'), XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (empty($return)) $return = '';

    // get text so we can do security check
    $text = xarModAPIFunc('bible', 'user', 'get',
                          array('tid' => $tid));

    // Check for exceptions
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // security check
    if (!xarSecurityCheck('AddBible', 1, 'Text', "$text[sname]:$tid")) {
        return;
    }

    // change state
    if (!xarModAPIFunc('bible', 'admin', 'setstate',
                       array('tid' => $tid, 'newstate' => 1))) {
        return;
    }

    // now send to the page where we create the index
    if (empty($return)) $return = xarModURL('bible', 'admin', 'view');
    xarResponseRedirect($return);

    // Return
    return true;
}

?>
