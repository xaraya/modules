<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Get all HTML tags
 *
 * @public
 * @author John Cox 
 * @purifiedby Richard Cave 
 * @param $args['startnum'] optional
 * @param $args['numitems'] optional
 * @returns array
 * @return array of HTML tags, or false on failure
 */
function html_userapi_getall($args)
{
    // Extract arguments
    extract($args);

    // Set empty array
    $htmls = array();

    // Security Check
	if (!xarSecurityCheck('ReadHTML')) {
        return $htmls;
    }

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Set HTML table
    $htmltable = $xartable['html'];

    // Get HTML tags
    $query = "SELECT xar_cid,
                     xar_tag,
                     xar_allowed
            FROM $htmltable
            ORDER BY xar_tag";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);

    // Check for an error
    if (!$result) return;

    // Put HTML tags into an array
    for (; !$result->EOF; $result->MoveNext()) {
        list($cid,
             $tag,
             $allowed) = $result->fields;

         $htmls[] = array('cid'       => $cid,
                          'htmltag'   => $tag,
                          'allowed'   => $allowed);
    }

    // Close result set
    $result->Close();

    return $htmls;
}

?>
