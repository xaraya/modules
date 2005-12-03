<?php
/**
 * File: $Id:
 *
 * Standard function to modify a text
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
 * modify a text
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @param  $ 'tid' the id of the text to be modified
 */
function bible_admin_modify($args)
{
    extract($args);

    if (!xarVarFetch('tid', 'int:1:', $tid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sname', 'str:1:', $sname, $sname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lname', 'str:1:', $lname, $lname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return', 'str:1:', $return, xarServerGetVar('HTTP_REFERER'), XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (!empty($objectid)) $tid = $objectid;
    if (empty($return)) $return = '';

    $text = xarModAPIFunc('bible', 'user', 'get', array('tid' => $tid, 'state' => 'all'));
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditBible', 1, 'Text', "$text[sname]:$text[tid]")) {
        return;
    }

    // Return the template variables defined in this function
    $data = xarModAPIFunc('bible', 'admin', 'menu');
    $data = array_merge($data, array(
        'authid'       => xarSecGenAuthKey(),
        'snamelabel'   => xarVarPrepForDisplay(xarML('Short Name')),
        'sname'        => $text['sname'],
        'lnamelabel'   => xarVarPrepForDisplay(xarML('Long Name')),
        'lname'        => $text['lname'],
        'invalid'      => $invalid,
        'updatebutton' => xarVarPrepForDisplay(xarML('Update Text')),
        'text'         => $text,
        'return'       => $return)
    );

    return $data;
}

?>
