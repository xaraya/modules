<?php
/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function bbcode_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['bbcode'];
    $query = "SELECT COUNT(1)
              FROM $table";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();
    return $numitems;
}
?>