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
 * Utility function to count the number of issues in a publication
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['publicationId'] publication the issue belongs to
 * @returns integer
 * @return number of items
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_countissues($args)
{
    // Security check
    if(!xarSecurityCheck('OverviewNewsletter')) return;

    // Get arguments from argument array
    extract($args);

    // Set defaults
    if (!isset($display)) {
        $display = 'unpublished';
    }

    if (!isset($publicationId)) {
        $publicationId = 0;
    }

    if (!isset($owner)) {
        $owner = 0;
    }


    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $issuesTable = $xartable['nwsltrIssues'];

    // Create query to select issues
    if ($publicationId) {
        // Get issues for a publication
        $query = "SELECT COUNT(1)
                  FROM  $issuesTable
                  WHERE $issuesTable.xar_pid = " . $publicationId . "
                  AND   $issuesTable.xar_pid != 0";
    } else {
        // Get all issues
        $query = "SELECT COUNT(1) FROM $issuesTable
                  WHERE $issuesTable.xar_pid != 0";
    }

    switch ($display) {
        case 'published':
            $query .= " AND $issuesTable.xar_datepublished > 0";
            break;
        case 'unpublished':
        default:
            $query .= " AND $issuesTable.xar_datepublished = 0";
            break;
    }

    if ($owner) {
        $query .= " AND $issuesTable.xar_ownerid = " . $owner;
    }

    $result =& $dbconn->Execute($query);

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
