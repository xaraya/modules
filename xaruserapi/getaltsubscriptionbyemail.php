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
 * Get an Newsletter alternative subscription
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['email'] email of the subscription to get
 * @param $args['pid'] publication id of the subscription to get - optional
 * @returns subscription array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_getaltsubscriptionbyemail($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($email) || !is_string($email)) {
        $invalid[] = 'email';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getaltsubscriptionbyemail', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrAltSubscriptions'];
    $query = "SELECT xar_id, 
                     xar_name,
                     xar_email,
                     xar_pid,
                     xar_htmlmail
              FROM $nwsltrTable
              WHERE xar_email = ?";

    $bindvars[] = (string) $email;

    if(isset($pid)) {
        $query .= " AND xar_pid = ?";
        $bindvars[] = (int) $pid;
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
    list($id, 
         $name, 
         $email, 
         $pid, 
         $htmlmail) = $result->fields;

    // Close result set
    $result->Close();

    // Create the subscription array
    $subscription = array('id' => $id,
                          'name' => $name,
                          'email' => $email,
                          'pid' => $pid,
                          'htmlmail' => $htmlmail);

    // Return the subscription array
    return $subscription;
}

?>
