<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://xavier.schwabfoundation.org
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * search alternative subscription by publication or user name/email
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
function newsletter_adminapi_searchaltsubscription($args)
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
                    join(', ',$invalid), 'adminapi', 'searchaltsubscription', 'Newsletter');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $items = array();

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrAltSubTable = $xartable['nwsltrAltSubscriptions'];
    $nwsltrPubTable = $xartable['nwsltrPublications'];

    // Switch to requested view
    switch(strtolower($search)) {

        case 'publication':
            // Get subscriptions by publication
            $query = "SELECT $nwsltrAltSubTable.xar_id,
                             $nwsltrAltSubTable.xar_pid,
                             $nwsltrPubTable.xar_title,
                             $nwsltrAltSubTable.xar_name,
                             $nwsltrAltSubTable.xar_email
                      FROM   $nwsltrAltSubTable, $nwsltrPubTable
                      WHERE  $nwsltrPubTable.xar_id = " . xarVarPrepForStore($pid) . "
                      AND    $nwsltrAltSubTable.xar_pid = $nwsltrPubTable.xar_id";

            if (!empty($searchname)) {
                $query .= " AND ($nwsltrAltSubTable.xar_name LIKE '%" . $searchname . "%' OR $nwsltrAltSubTable.xar_email LIKE  '%" . $searchname . "%')";
            }
   
            $query .= " ORDER by $nwsltrAltSubTable.xar_name";

            break;

        case 'email':
            // Get subscriptions by email
            $query = "SELECT $nwsltrAltSubTable.xar_id,
                             $nwsltrAltSubTable.xar_pid,
                             $nwsltrPubTable.xar_title,
                             $nwsltrAltSubTable.xar_name,
                             $nwsltrAltSubTable.xar_email
                      FROM   $nwsltrAltSubTable, $nwsltrPubTable
                      WHERE  $nwsltrAltSubTable.xar_pid = $nwsltrPubTable.xar_id
                      AND    $nwsltrAltSubTable.xar_email LIKE '%" . $searchname . "%'
                      ORDER by $nwsltrAltSubTable.xar_name";

            break;

        case 'name':
        default:
            // Get subscriptions by name
            $query = "SELECT $nwsltrAltSubTable.xar_id,
                             $nwsltrAltSubTable.xar_pid,
                             $nwsltrPubTable.xar_title,
                             $nwsltrAltSubTable.xar_name,
                             $nwsltrAltSubTable.xar_email
                      FROM   $nwsltrAltSubTable, $nwsltrPubTable
                      WHERE  $nwsltrAltSubTable.xar_pid = $nwsltrPubTable.xar_id
                      AND   ($nwsltrAltSubTable.xar_name LIKE  '%" . $searchname . "%')
                      ORDER by $nwsltrAltSubTable.xar_name";

            break;
    }

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

    // Check for an error
    if (!$result) return;

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id, 
             $pid, 
             $title, 
             $name, 
             $email) = $result->fields;

         $items[] = array('id' => $id,
                          'pid' => $pid,
                          'title' => $title,
                          'name' => $name,
                          'email' => $email);
    }

    // Close result set
    $result->Close();

    // Return the items
    return $items;
}

?>
