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
 * Utility function to count the number of items
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['phase'] type of item to count (ie 'story', 'publcation', etc.)
 * @param $args['owner'] owner of item to count
 * @returns integer
 * @return number of items
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_countitems($args)
{
    // Security check
    if(!xarSecurityCheck('OverviewNewsletter')) return;

    // Get arguments from argument array
    extract($args);

    if (!isset($phase) || !is_string($phase)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'phase', 'userapi', 'countitems', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check for option vars
    if (!isset($owner))
        $owner = false;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Switch to requested view and create SQL statement
    switch(strtolower($phase)) {
        case 'owner':
            // Name the table and column definitions
            $nwsltrTable = $xartable['nwsltrOwners'];
            break;

        case 'disclaimer':
            // Name the table and column definitions
            $nwsltrTable = $xartable['nwsltrDisclaimers'];
            break;

        case 'story':
            // Name the table and column definitions
            $nwsltrTable = $xartable['nwsltrStories'];
            break;

        case 'topic':
            // Name the table and column definitions
            $nwsltrTable = $xartable['nwsltrTopics'];
            break;

        case 'issue':
            // Name the table and column definitions
            $nwsltrTable = $xartable['nwsltrIssues'];
            break;

        case 'publication':
            // Name the table and column definitions
            $nwsltrTable = $xartable['nwsltrPublications'];
            break;

        case 'subscription':
            // Name the table and column definitions
            $nwsltrTable = $xartable['nwsltrSubscriptions'];
            break;

        default:
            break;
    }

    // Get item
    $query = "SELECT COUNT(1)
              FROM $nwsltrTable";

    $where = false;
    if (isset($display)) {
        switch ($display) {
            case 'published':
                $query .= " WHERE xar_datepublished > 0";
                $where = true;
                break;
            case 'unpublished':
                $query .= " WHERE xar_datepublished = 0";
                $where = true;
                break;
        }
    }

    $bindvars = array();
    if ($owner) {
        // Get current uid
        $userid = xarSessionGetVar('uid');

        if ($where) {
            $query .= " AND ";
        } else {
            $query .= " WHERE ";
        }
        $query .= "xar_ownerid = ?";
        $bindvars[] = (int) $userid;
    }

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Obtain the number of items
    list($numitems) = $result->fields;

    // Close result set
    $result->Close();

    // Return the number of items
    return $numitems;
}

?>
