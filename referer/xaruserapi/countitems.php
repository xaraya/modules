<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Referers
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Referer Module
 * @author John Cox et al. 
 */

/**
 * utility function to count the number of items held by this module
 * 
 * @author the Example module development team 
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function referer_userapi_countitems()
{ 
    // Security Check
    if (!xarSecurityCheck('OverviewReferer')) return; 
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $referertable = $xartable['referer']; 
    // Get item
    $query = "SELECT COUNT(1)
            FROM $referertable";
    $result = &$dbconn->Execute($query);

    if (!$result) return; 
    // Obtain the number of items
    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;
} 

?>