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
 * @param $args['search'] the type of search to perform ('publication', 'email', 'name')
 * @param $args['searchname'] the name publication, email, name to search for
 * @param $args['pid'] the publication id to search for - optional
 * @param $args['startnum'] start with this item number (default 1)
 * @param $args['numitems'] the number of items to retrieve (default -1 = all)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_adminapi_searchsubscription($args)
{
    // Get arguments
    extract($args);

    // Optional arguments.
    if (!isset($searchname)) {
        $searchname = '';
    }
    if (!isset($pid)) {
        $pid = 0;
    }
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
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Initialize arrays
    $items = array();
    $deleteitems = array();

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrSubTable = $xartable['nwsltrSubscriptions'];
    $nwsltrAltSubTable = $xartable['nwsltrAltSubscriptions'];
    $nwsltrPubTable = $xartable['nwsltrPublications'];
    $rolesTable = $xartable['roles'];

    // Switch to requested view
    switch(strtolower($search)) {

        case 'publication':
            if (!$pid) {
                // Get subscriptions for all publications
                $query = "SELECT $rolesTable.xar_uid,
                                 $nwsltrSubTable.xar_pid,
                                 $nwsltrPubTable.xar_title,
                                 $rolesTable.xar_name,
                                 $rolesTable.xar_uname,
                                 $rolesTable.xar_email,
                                 $rolesTable.xar_state,
                                 0 as xar_type,
                                 $nwsltrSubTable.xar_htmlmail
                          FROM   $nwsltrSubTable, $nwsltrPubTable, $rolesTable
                          WHERE  $nwsltrSubTable.xar_uid =  $rolesTable.xar_uid
                          AND    $nwsltrSubTable.xar_pid = $nwsltrPubTable.xar_id";

                if (!empty($searchname)) {
                    $query .= " AND ($rolesTable.xar_name LIKE '%" . $searchname . "%' OR $rolesTable.xar_email LIKE  '%" . $searchname . "%')";
                }

                // Union
                $query .= " UNION ";

                // Get alt subscriptions by publication
                $query .= "SELECT $nwsltrAltSubTable.xar_id,
                                  $nwsltrAltSubTable.xar_pid,
                                  $nwsltrPubTable.xar_title,
                                  $nwsltrAltSubTable.xar_name,
                                  $nwsltrAltSubTable.xar_name as uname,
                                  $nwsltrAltSubTable.xar_email,
                                  3 as xar_state,
                                  1 as xar_type,
                                  $nwsltrAltSubTable.xar_htmlmail
                           FROM   $nwsltrAltSubTable, $nwsltrPubTable
                           WHERE  $nwsltrAltSubTable.xar_pid = $nwsltrPubTable.xar_id";

                if (!empty($searchname)) {
                    $query .= " AND ($nwsltrAltSubTable.xar_name LIKE '%" . $searchname . "%' OR $nwsltrAltSubTable.xar_email LIKE  '%" . $searchname . "%')";
                }

                $query .= " ORDER BY xar_pid";
   
            } else {
            
                // Get subscriptions by publication
                $query = "SELECT $rolesTable.xar_uid,
                                 $nwsltrSubTable.xar_pid,
                                 $nwsltrPubTable.xar_title,
                                 $rolesTable.xar_name,
                                 $rolesTable.xar_uname,
                                 $rolesTable.xar_email,
                                 $rolesTable.xar_state,
                                 0 as xar_type,
                                 $nwsltrSubTable.xar_htmlmail
                          FROM   $nwsltrSubTable, $nwsltrPubTable, $rolesTable
                          WHERE  $nwsltrSubTable.xar_uid =  $rolesTable.xar_uid
                          AND    $nwsltrPubTable.xar_id = ?
                          AND    $nwsltrSubTable.xar_pid = $nwsltrPubTable.xar_id";

                $bindvars[] = (int) $pid;

                if (!empty($searchname)) {
                    $query .= " AND ($rolesTable.xar_name LIKE '%" . $searchname . "%' OR $rolesTable.xar_email LIKE  '%" . $searchname . "%')";
                }

                // Union
                $query .= " UNION ";

                // Get alt subscriptions by publication
                $query .= "SELECT $nwsltrAltSubTable.xar_id,
                                  $nwsltrAltSubTable.xar_pid,
                                  $nwsltrPubTable.xar_title,
                                  $nwsltrAltSubTable.xar_name,
                                  $nwsltrAltSubTable.xar_name as uname,
                                  $nwsltrAltSubTable.xar_email,
                                  3 as xar_state,
                                  1 as xar_type,
                                  $nwsltrAltSubTable.xar_htmlmail
                           FROM   $nwsltrAltSubTable, $nwsltrPubTable
                           WHERE  $nwsltrPubTable.xar_id = ? 
                           AND    $nwsltrAltSubTable.xar_pid = $nwsltrPubTable.xar_id";

                $bindvars[] = (int) $pid;

                if (!empty($searchname)) {
                    $query .= " AND ($nwsltrAltSubTable.xar_name LIKE '%" . $searchname . "%' OR $nwsltrAltSubTable.xar_email LIKE  '%" . $searchname . "%')";
                }
   
                $query .= " ORDER BY xar_pid";
            }

            break;

        case 'email':
            // Get subscriptions by email
            $query = "SELECT $rolesTable.xar_uid,
                             $nwsltrSubTable.xar_pid,
                             $nwsltrPubTable.xar_title,
                             $rolesTable.xar_name,
                             $rolesTable.xar_uname,
                             $rolesTable.xar_email,
                             $rolesTable.xar_state,
                             0 as xar_type,
                             $nwsltrSubTable.xar_htmlmail
                      FROM   $nwsltrSubTable, $nwsltrPubTable, $rolesTable
                      WHERE  $nwsltrSubTable.xar_uid =  $rolesTable.xar_uid
                      AND    $nwsltrSubTable.xar_pid = $nwsltrPubTable.xar_id";

            if (!empty($searchname)) {
                $query .= " AND $rolesTable.xar_email LIKE  '%" . $searchname . "%'";
            }

            // Union
            $query .= " UNION ";

            // Get alt subscriptions by email
            $query .= "SELECT $nwsltrAltSubTable.xar_id,
                              $nwsltrAltSubTable.xar_pid,
                              $nwsltrPubTable.xar_title,
                              $nwsltrAltSubTable.xar_name,
                              $nwsltrAltSubTable.xar_name as uname,
                              $nwsltrAltSubTable.xar_email,
                              3 as xar_state,
                              1 as xar_type,
                              $nwsltrAltSubTable.xar_htmlmail
                       FROM   $nwsltrAltSubTable, $nwsltrPubTable
                       WHERE  $nwsltrAltSubTable.xar_pid = $nwsltrPubTable.xar_id";

            if (!empty($searchname)) {
                $query .= " AND $nwsltrAltSubTable.xar_email LIKE  '%" . $searchname . "%'";
            }

            $query .= " ORDER BY xar_email";

            break;

        case 'name':
        default:
            // Get subscriptions by name
            $query = "SELECT $rolesTable.xar_uid,
                             $nwsltrSubTable.xar_pid,
                             $nwsltrPubTable.xar_title,
                             $rolesTable.xar_name,
                             $rolesTable.xar_uname,
                             $rolesTable.xar_email,
                             $rolesTable.xar_state,
                             0 as xar_type,
                             $nwsltrSubTable.xar_htmlmail
                      FROM   $nwsltrSubTable, $nwsltrPubTable, $rolesTable
                      WHERE  $nwsltrSubTable.xar_uid =  $rolesTable.xar_uid
                      AND    $nwsltrSubTable.xar_pid = $nwsltrPubTable.xar_id";

            if (!empty($searchname)) {
                // Do we want to search both the uname and name columns?
                //$query .= " AND ($rolesTable.xar_uname LIKE '%" . $searchname . "%' OR
                //                 $rolesTable.xar_name LIKE  '%" . $searchname . "%')";
                $query .= " AND ($rolesTable.xar_name LIKE  '%" . $searchname . "%')";
            }

            // Union
            $query .= " UNION ";

            // Get alt subscriptions by name
            $query .= "SELECT $nwsltrAltSubTable.xar_id,
                              $nwsltrAltSubTable.xar_pid,
                              $nwsltrPubTable.xar_title,
                              $nwsltrAltSubTable.xar_name,
                              $nwsltrAltSubTable.xar_name as uname,
                              $nwsltrAltSubTable.xar_email,
                              3 as xar_state,
                              1 as xar_type,
                              $nwsltrAltSubTable.xar_htmlmail
                       FROM   $nwsltrAltSubTable, $nwsltrPubTable
                       WHERE  $nwsltrAltSubTable.xar_pid = $nwsltrPubTable.xar_id";

            if (!empty($searchname)) {
                $query .= " AND $nwsltrAltSubTable.xar_name LIKE  '%" . $searchname . "%'";
            }

            $query .= " ORDER BY xar_name";

            break;
    }

    if (isset($bindvars) && !empty($bindvars)) {
        $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    } else {
        $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    }

    // Check for an error
    if (!$result) return;

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($uid, $pid, $title, $name, $uname, $email, $state, $type, $htmlmail) = $result->fields;

        // Determine if a user state has been set to ROLES_STATE_DELETED (0)
        if ($state == 0) {
            $deleteitems[] = array('uid' => $uid,
                                   'type' => $type);
        } else {
            $items[] = array('uid' => $uid,
                             'pid' => $pid,
                             'title' => $title,
                             'name' => $name,
                             'uname' => $uname,
                             'email' => $email,
                             'state' => $state,
                             'type' => $type,
                             'htmlmail' => $htmlmail);
        }
    }

    // Close result set
    $result->Close();

    // Delete any users that have been set to ROLES_STATE_DELETED (0)
    if (!empty($deleteitems)) {
        foreach ($deleteitems as $subscription) {
            // Remove this subscription
            if ($subscription['type'] == 0) {
                $result = xarModAPIFunc('newsletter',
                                        'admin',
                                        'deletesubscription',
                                        array('uid' => $subscription['uid']));
            } else {
                $result = xarModAPIFunc('newsletter',
                                        'admin',
                                        'deletealtsubscription',
                                        array('id' => $subscription['uid']));
            }
        }
    }

    // Return the items
    return $items;
}

?>
