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
 * Count the number of HTML tags in the database
 * 
 * @public
 * @author John Cox 
 * @author Richard Cave 
 * @param none
 * @returns integer
 * @returns number of HTML tags in the database
 * @raise none
 */
function html_userapi_countitems()
{
    // Security Check
	if(!xarSecurityCheck('ReadHTML')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set HTML table
    $htmltable = $xartable['html'];

    // Count number of items in table
    $query = "SELECT COUNT(1)
              FROM $htmltable";
    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return false;

    // Get number of items
    list($numitems) = $result->fields;

    // Close result set
    $result->Close();

    // Return number of items
    return $numitems;
}

?>
