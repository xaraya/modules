<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Create a subscription
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['uid'] user id
 * @param $args['pid'] publication id 
 * @param $args['htmlmail'] send mail in html or text format (1 = html, 0 = text)
 * @returns int
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_userapi_createsubscription($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($uid) || !is_numeric($uid)) {
        $invalid[] = 'User ID';
    }
    if (!isset($pid) || !is_numeric($pid)) {
        $invalid[] = 'title';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'createsubscription', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrSubscriptions'];

    // Check if that subscription already exists
     $query = "SELECT xar_uid FROM $nwsltrTable
               WHERE xar_uid = ?
               AND   xar_pid = ?";

    $result =& $dbconn->Execute($query, array((int) $uid, (int) $pid));
    if (!$result) return false;

    if ($result->RecordCount() > 0) {
        return false;  // subscription already exists
    }

    // Add item
    $query = "INSERT INTO $nwsltrTable (
              xar_uid,
              xar_pid,
              xar_htmlmail)
            VALUES (?, ?, ?)";

    $bindvars = array((int) $uid, (int) $pid, (int) $htmlmail);

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return false;

    // Let any hooks know that we have created a new item
    xarModCallHooks('item', 'createsubscription', $uid, 'uid');

    // Return true
    return true;
}

?>
