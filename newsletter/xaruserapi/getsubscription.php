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
 * Get an Newsletter subscription by id
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['uid'] user id of subscription to get
 * @param $args['pid'] publication id of subscription to get - optional
 * @returns subscription array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_getsubscription($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($uid) || !is_numeric($uid)) {
        $invalid[] = 'user id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getsubscription', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $subTable = $xartable['nwsltrSubscriptions'];
    $rolesTable = $xartable['roles'];

    $query = "SELECT $subTable.xar_uid,
                     $rolesTable.xar_name,
                     $rolesTable.xar_uname,
                     $rolesTable.xar_email,
                     $subTable.xar_pid,
                     $subTable.xar_htmlmail
              FROM  $subTable, $rolesTable
              WHERE $subTable.xar_uid = ? 
              AND   $subTable.xar_uid = $rolesTable.xar_uid";

    $bindvars[] = (int) $uid;

    if(isset($pid)) {
        $bindvars[] = (int) $pid;
        $query .= " AND $subTable.xar_pid = ? ";
    }

    // Process query
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Check for no rows found
    if ($result->EOF) {
        $result->Close();
        return;
    }

    // Obtain the subscription information from the result set
    list($uid, 
         $name, 
         $uname, 
         $email, 
         $pid, 
         $htmlmail) = $result->fields;

    // Close result set
    $result->Close();

    // Create the subscription array
    $subscription = array('uid' => $uid,
                          'name' => $name,
                          'uname' => $uname,
                          'email' => $email,
                          'pid' => $pid,
                          'htmlmail' => $htmlmail);

    // Return the subscription array
    return $subscription;
}

?>
