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
 * Get an Newsletter owner by id
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of newsletter owner to get
 * @returns owner array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_getowner($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getowner', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrOwners'];

    $query = "SELECT xar_uid,
                     xar_rid,
                     xar_signature
                FROM $nwsltrTable
               WHERE xar_uid = ?";

    // Process query
    $result =& $dbconn->Execute($query, array((int) $id));

    // Check for an error
    if (!$result) return;

    // Check for no rows found
    if ($result->EOF) {
        $result->Close();
        return;
    }

    // Obtain the owner information from the result set
    list($uid, 
         $rid, 
         $signature) = $result->fields;

    // Close result set
    $result->Close();

    // The user API function is called.
    $userData = xarModAPIFunc('roles',
                              'user',
                              'get',
                              array('uid' => $uid));

    if ($userData == false) {
        // If this user does not exist in xar_roles table
        // then show as unknown
        $ownerName = "Unknown User";
    } else {
        // Create the owner
        $ownerName = $userData['name'];
    }

    // Creat the owner array
    $owner = array('id' => $uid,
                   'name' => $ownerName,
                   'rid' => $rid,
                   'signature' => $signature);

    // Return the owner array
    return $owner;
}

?>
