<?php
/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function pmember_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['pmember'];

    $query = "SELECT COUNT(1)
              FROM $table";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();
    return $numitems;
}
?>