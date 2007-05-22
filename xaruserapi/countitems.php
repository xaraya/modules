<?php
/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function ping_userapi_countitems()
{
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    // Security Check
    if(!xarSecurityCheck('Readping')) return;
    $table = $xartable['ping'];
    $query = "SELECT COUNT(1)
            FROM $table";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();
    return $numitems;
}
?>