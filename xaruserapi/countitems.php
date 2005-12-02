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
function bible_userapi_countitems()
{ 
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables(); 

    $texttable = $xartable['bible_texts']; 

    $query = "SELECT COUNT(1)
            FROM $texttable";
    $result = $dbconn->Execute($query,array()); 

    if (!$result) return; 

    // Obtain the number of texts
    list($numitems) = $result->fields; 

    $result->Close(); 

    // Return the number of texts
    return $numitems;
} 

?>
