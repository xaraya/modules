<?php

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function headlines_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Security Check
	if(!xarSecurityCheck('OverviewHeadlines')) return;

    $headlinestable = $xartable['headlines'];

    $query = "SELECT COUNT(1)
            FROM $headlinestable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>
