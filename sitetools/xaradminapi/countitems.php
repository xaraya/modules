<?php
/*
 * File: $Id: $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by jojodee
 * @link http://xaraya.athomeandabout.come
 *
 * @subpackage SiteTools module
 * @author Jo Dalle Nogare <http://xaraya.athomeandabout.com  contact:jojodee@xaraya.com>
*/


/*
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
*/
function sitetools_adminapi_countitems()
{ 
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables(); 

    $sitetoolstable = $xartable['sitetools'];

    $query = "SELECT COUNT(1)
            FROM $sitetoolstable";
    $result = &$dbconn->Execute($query);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Obtain the number of items
    list($numitems) = $result->fields;
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the number of items
    return $numitems;
}
?>
