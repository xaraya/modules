<?php
/**
 * utility function to count the number of items held by this module
 *
 * @author the Ephemerid 
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function ephemerids_userapi_countitems()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // Security Check
    if(!xarSecurityCheck('OverviewEphemerids')) return;
    $ephemtable = $xartable['ephem'];
    // Get item 
    $query = "SELECT COUNT(1)
            FROM $ephemtable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Obtain the number of items
    list($numitems) = $result->fields;
    $result->Close();
    // Return the number of items
    return $numitems;
}
?>