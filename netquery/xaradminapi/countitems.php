<?php
/**
 * count the number of links in the database
 */
 
function netquery_adminapi_countitems()
{
    if(!xarSecurityCheck('OverviewNetquery')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];

    $query = "SELECT COUNT(1) FROM $WhoisTable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>