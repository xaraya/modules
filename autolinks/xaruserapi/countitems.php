<?php

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function autolinks_userapi_countitems()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    $query = "SELECT COUNT(1)
            FROM $autolinkstable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>