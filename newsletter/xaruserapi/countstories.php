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
 * Utility function to count the number of stories in an issue
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['issueId'] issue the story belongs to
 * @param $args['owner'] count only logged user stories (1=true, 0=false)
 * @param $args['display'] count 'published' or 'unpublished' or 'all' stories
 * @returns integer
 * @return number of items
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_countstories($args)
{
    // Security check
    if(!xarSecurityCheck('OverviewNewsletter')) return;

    // Get arguments from argument array
    extract($args);

    // Set defaults
    if (!isset($owner)) {
        $owner = 0;
    }

    if (!isset($display)) {
        $display = 'unpublished';
    }

    if (!isset($issueId)) {
        $issueId = 0;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $topicsTable = $xartable['nwsltrTopics'];
    $storiesTable = $xartable['nwsltrStories'];

    // Create query to select stories
    $bindvars = array();
    if ($issueId) {
        // Get stories for to a certain issue
        $query = "SELECT COUNT(1)
                  FROM  $topicsTable, $storiesTable
                  WHERE $topicsTable.xar_issueid = ?
                  AND   $topicsTable.xar_storyid = $storiesTable.xar_id
                  AND   $storiesTable.xar_pid != 0";

        $bindvars[] = (int) $issueId;
    } else {
        // Get all stories
        $query = "SELECT COUNT(1)
                  FROM $storiesTable
                  WHERE $storiesTable.xar_pid != 0";
    }

    // Check if showing stories created by a particular author
    if ($owner) {
        $userid = xarSessionGetVar('uid');
        $query .= " AND   $storiesTable.xar_ownerid = ?";
        $bindvars[] = (int) $userid;
    }

    // Check display type
    switch ($display) {
        case 'published':
            $query .= " AND $storiesTable.xar_datepublished > 0";
            break;
        case 'unpublished':
            $query .= " AND $storiesTable.xar_datepublished = 0";
            break;
        case 'all':
        default:
            $query .= " AND $storiesTable.xar_datepublished >= 0";
            break;
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
