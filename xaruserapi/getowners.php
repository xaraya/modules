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
 * Get all Newsletter owners
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['numitems'] the number of items to retrieve (default -1 = all)
 * @param $args['startnum'] start with this item number (default 1)
 * @param $args['display'] display 'published' or 'unpublished' stories/issues
 * @param $args['owner'] get stories/issues for this owner (1 = true, 0 = false)
 * @param $args['sortby'] sort by 'title', 'category', 'publication', 'date' or 'owner'
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_getowners($args)
{
    // Get arguments
    extract($args);

    // Optional arguments.
    if(!isset($startnum)) {
        $startnum = 1;
    }

    if (!isset($numitems)) {
        $numitems = -1;

    }

    // Argument check
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'get', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $items = array();

    // Security check
    if(!xarSecurityCheck('OverviewNewsletter')) return;

    // Load categories API
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API','categories','user');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MODULE_DEPENDENCY', new SystemException($msg));
        return false;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set roles and categories table
    $rolesTable = $xartable['roles'];
    $categoriesTable = $xartable['categories'];

    // Name the table and column definitions
    $ownersTable = $xartable['nwsltrOwners'];

    // Get items
    $query = "SELECT $ownersTable.xar_uid,
                     $ownersTable.xar_rid,
                     $rolesTable.xar_name,
                     $ownersTable.xar_signature
              FROM $ownersTable, $rolesTable
              WHERE $ownersTable.xar_uid = $rolesTable.xar_uid
              ORDER BY $rolesTable.xar_name";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

    // Check for an error
    if (!$result) return;

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($uid, 
             $rid, 
             $ownerName,
             $signature) = $result->fields;

        $items[] = array('id' => $uid,
                         'rid' => $rid,
                         'name' => $ownerName,
                         'signature' => $signature);
    }

    // Close result set
    $result->Close();

    // Return the items
    return $items;
}

?>
