<?php
/**
 * File: $Id:
 * 
 * Utility function counts number of items held by this module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage MyBookMarks
 * @author John Cox
 */
/**
 * utility function to count the number of items held by this module
 * 
 * @author John Cox
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function MyBookMarks_userapi_countitems()
{ 
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['mybookmarks']; 
    // Get item
    $query = "SELECT COUNT(1)
            FROM $table";
    $result =& $dbconn->Execute($query); 
    if (!$result) return; 
    // Obtain the number of items
    list($numitems) = $result->fields; 
    $result->Close(); 
    // Return the number of items
    return $numitems;
} 
?>