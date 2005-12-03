<?php
/**
 * File: $Id:
 *
 * Update a text
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
 * update a text
 *
 * @author the Example module development team
 * @param  $args ['tid'] the ID of the text
 * @param  $args ['sname'] the short name of the text
 * @param  $args ['lname'] the long name of the text
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bible_adminapi_update($args)
{
    extract($args);

    // set defaults
    if (!isset($lname)) $lname = '';

    $invalid = array();
    if (!isset($tid) || !is_numeric($tid)) {
        $invalid[] = 'tid';
    }
    if (!isset($sname) || !is_string($sname)) {
        $invalid[] = 'sname';
    }
    if (!empty($lname) && !is_string($lname)) {
        $invalid[] = 'lname';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'update', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // get text
    $text = xarModAPIFunc('bible', 'user', 'get', array('tid' => $tid, 'state' => 2));
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check - need to do both, since short name is in the security schema
    if (!xarSecurityCheck('EditBible', 1, 'Text', "$text[sname]:$tid")) {
        return;
    }
    if (!xarSecurityCheck('EditBible', 1, 'Text', "$sname:$tid")) {
        return;
    }

    $textsdbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $textstable = $xartable['bible_texts'];

    // store new params in database
    $query = "
        UPDATE $textstable
        SET xar_sname = ?, xar_lname = ?
        WHERE xar_tid = ?
    ";
    $bindvars = array($sname, $lname, $tid);

    $textsdbconn->Execute($query, $bindvars);
    if ($textsdbconn->ErrorNo()) return;

    // success
    return true;
}

?>
