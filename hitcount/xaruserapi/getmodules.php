<?php

/**
 * get the list of modules for which we're counting items
 *
 * @returns array
 * @return $array[$modid][$itemtype] = array('items' => $numitems,'hits' => $numhits);
 */
function hitcount_userapi_getmodules($args)
{
// Security Check
	if(!xarSecurityCheck('ViewHitcountItems')) return;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $hitcounttable = $xartable['hitcount'];

    // Get items
    $query = "SELECT xar_moduleid, xar_itemtype, COUNT(xar_itemid), SUM(xar_hits)
            FROM $hitcounttable
            GROUP BY xar_moduleid, xar_itemtype";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numitems,$numhits) = $result->fields;
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'hits' => $numhits);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
