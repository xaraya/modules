<?php

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function xarbb_userapi_countforums()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    $query = "SELECT COUNT(1)
            FROM $xbbforumstable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>