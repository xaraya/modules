<?php
/**
 * File: $Id:
 *
 * Utility function counts number of texts held by this module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
/**
 * utility function to count the number of texts held by this module
 *
 * @author curtisdf
 * @returns integer
 * @return number of texts held by this module
 * @raise DATABASE_ERROR
 */
function bible_userapi_countitems($args)
{
    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    extract($args);

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $texttable = $xartable['bible_texts'];

    // generate query
    $query = "SELECT COUNT(1) FROM $texttable WHERE 1";
    $bindvars = array();
    if (!empty($state) && is_numeric($state)) {
        $query .= "AND xar_state = ?";
        $bindvars[] = $state;
    }

    // execute query
    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // retrieve the number and close db connection
    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;
}

?>
