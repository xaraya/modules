<?php

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function smilies_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Security Check
	if(!xarSecurityCheck('OverviewSmilies')) return;

    $smiliestable = $xartable['smilies'];

    $query = "SELECT COUNT(1)
            FROM $smiliestable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>