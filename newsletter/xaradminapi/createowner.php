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
 * Create an Newsletter owner
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of the owner (uid in roles)
 * @param $args['rid'] group of the owner
 * @param $args['signature'] the owner's signature
 * @returns newsletter owner ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_createowner($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($rid) || !is_numeric($rid)) {
        $invalid[] = 'rid';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'createowner', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrOwners'];

    // Check if that owner already exists
    $query = "SELECT xar_uid FROM $nwsltrTable
              WHERE xar_uid = ?";

    $result =& $dbconn->Execute($query, array((int) $id));
    if (!$result) return false; 

    if ($result->RecordCount() > 0) {
        return false;  // owner already exists
    }

    // Check for signature
    if (!isset($signature)) {
        // Add item
        $query = "INSERT INTO $nwsltrTable (
                  xar_uid,
                  xar_rid)
                  VALUES ( ?, ? )";
        $bindvars = array((int) $id, (int) $rid);
    } else {
        // Add item
        $query = "INSERT INTO $nwsltrTable (
                  xar_uid,
                  xar_rid,
                  xar_signature)
                VALUES (?, ?, ?)";
        $bindvars = array((int) $id, (int) $rid, (string) $signature);
    }

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return false;

    // Let any hooks know that we have created a new item
    xarModCallHooks('item', 'create', $id, 'id');

    // Return the id of the newly created item to the calling process
    return $id;
}

?>
