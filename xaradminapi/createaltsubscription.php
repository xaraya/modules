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
 * Create an alternative subscription
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['name'] the name of the new subscription
 * @param $args['email'] the email address of the new subscription
 * @param $args['pid'] publication id 
 * @param $args['htmlmail'] send mail in html or text format (1 = html, 0 = text)
 * @returns int
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_createaltsubscription($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($email) || !is_string($email)) {
        $invalid[] = 'email';
    }
    if (!isset($pid) || !is_numeric($pid)) {
        $invalid[] = 'title';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'createaltsubscription', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!isset($htmlmail) || !is_numeric($htmlmail)) {
        $htmlmail = 0;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrAltSubscriptions'];

    // Check if that subscription already exists
    $query = "SELECT xar_email FROM $nwsltrTable
              WHERE xar_email = ?
              AND   xar_pid = ?";

    $result =& $dbconn->Execute($query, array((string) $email, (int) $pid));
    if (!$result) return false;

    if ($result->RecordCount() > 0) {
        //$msg = xarML('The email address already exists.');
        //xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return false;  // subscription already exists
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($nwsltrTable);

    // Add subscription
    $query = "INSERT INTO $nwsltrTable (
              xar_id,
              xar_name,
              xar_email,
              xar_pid,
              xar_htmlmail)
            VALUES (?, ?, ?, ?, ?)";

    $bindvars = array((int) $nextId, 
                      (string) $name,
                      (string) $email, 
                      (int) $pid,
                      (int) $htmlmail);

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return false;

    // Get the ID of the item that was inserted
    $subscriptionId = $dbconn->PO_Insert_ID($nwsltrTable, 'xar_id');

    // Let any hooks know that we have created a new item
    xarModCallHooks('item', 'create', $subscriptionId, 'subscriptionId');

    // Return true
    return $subscriptionId;
}

?>
