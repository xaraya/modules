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
 * search subscription by publication or user name/email
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['numitems'] the number of items to retrieve (default -1 = all)
 * @param $args['startnum'] start with this item number (default 1)
 * @param $args['search'] the type of search to perform ('publication', 'email', 'name')
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_adminapi_searchsubscription($args)
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

    if (!isset($search) || !is_string($search)) {
        $invalid[] = 'search';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'searchsubscription', 'Newsletter');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $items = array();

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrSubTable = $xartable['nwsltrSubscriptions'];
    $nwsltrPubTable = $xartable['nwsltrPublications'];
    $rolesTable = $xartable['roles'];

    // Switch to requested view
    switch(strtolower($search)) {

        case 'publication':
            // Get subscriptions
            $query = "SELECT $rolesTable.xar_uid,
                             $nwsltrSubTable.xar_pid,
                             $nwsltrPubTable.xar_title,
                             $rolesTable.xar_name,
                             $rolesTable.xar_uname,
                             $rolesTable.xar_email
                      FROM   $nwsltrSubTable, $nwsltrPubTable, $rolesTable
                      WHERE  $nwsltrSubTable.xar_uid =  $rolesTable.xar_uid
                      AND    $nwsltrPubTable.xar_id = " . xarVarPrepForStore($pid) . "
                      AND    $nwsltrSubTable.xar_pid = $nwsltrPubTable.xar_id";

            if (!empty($searchname)) {
                $query .= " AND ($rolesTable.xar_name LIKE '%" . $searchname . "%' OR $rolesTable.xar_email LIKE  '%" . $searchname . "%')";
            }
   
            $query .= " ORDER by $rolesTable.xar_name";

            break;

        case 'email':
            // Get items
            $query = "SELECT $rolesTable.xar_uid,
                             $nwsltrSubTable.xar_pid,
                             $nwsltrPubTable.xar_title,
                             $rolesTable.xar_name,
                             $rolesTable.xar_uname,
                             $rolesTable.xar_email
                      FROM   $nwsltrSubTable, $nwsltrPubTable, $rolesTable
                      WHERE  $nwsltrSubTable.xar_uid =  $rolesTable.xar_uid
                      AND    $nwsltrSubTable.xar_pid = $nwsltrPubTable.xar_id
                      AND    $rolesTable.xar_email LIKE '%" . $searchname . "%'
                      ORDER by $rolesTable.xar_name";

            break;

        case 'name':
        default:
            // Get items
            $query = "SELECT $rolesTable.xar_uid,
                             $nwsltrSubTable.xar_pid,
                             $nwsltrPubTable.xar_title,
                             $rolesTable.xar_name,
                             $rolesTable.xar_uname,
                             $rolesTable.xar_email
                      FROM   $nwsltrSubTable, $nwsltrPubTable, $rolesTable
                      WHERE  $nwsltrSubTable.xar_uid =  $rolesTable.xar_uid
                      AND    $nwsltrSubTable.xar_pid = $nwsltrPubTable.xar_id
                      AND   ($rolesTable.xar_uname LIKE '%" . $searchname . "%' OR
                             $rolesTable.xar_name LIKE  '%" . $searchname . "%')
                      ORDER by $rolesTable.xar_name";

            break;
    }

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

    // Check for an error
    if (!$result) return;

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($uid, $pid, $title, $name, $uname, $email) = $result->fields;

         $items[] = array('uid' => $uid,
                          'pid' => $pid,
                          'title' => $title,
                          'name' => $name,
                          'uname' => $uname,
                          'email' => $email);
    }

    // Close result set
    $result->Close();

    // Return the items
    return $items;
}

?>
