<?php

/**
 * get the list of modules for which we're counting items
 *
 * @returns array
 * @return $array[$modid][$itemtype] = $numitems
 */
function hitcount_userapi_getmodules($args)
{
// Security Check
	if(!xarSecurityCheck('ViewHitcountItems')) return;

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $hitcounttable = $xartable['hitcount'];

    // Get items
    $query = "SELECT xar_moduleid, xar_itemtype, COUNT(xar_itemid)
            FROM $hitcounttable
            GROUP BY xar_moduleid, xar_itemtype";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numitems) = $result->fields;
        $modlist[$modid][$itemtype] = $numitems;
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>