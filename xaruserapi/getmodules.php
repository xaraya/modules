<?php

/**
 * get the list of modules for which we're rating items
 *
 * @returns array
 * @return $array[$modid][$itemtype] = array('items' => $numitems,'ratings' => $numratings);
 */
function ratings_userapi_getmodules($args)
{
    // Security Check
    if (!xarSecurityCheck('OverviewRatings')) return;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ratingstable = $xartable['ratings'];

    // Get items
    $query = "SELECT xar_moduleid, xar_itemtype, COUNT(xar_itemid), SUM(xar_numratings)
            FROM $ratingstable
            GROUP BY xar_moduleid, xar_itemtype
            ORDER BY xar_moduleid, xar_itemtype";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numitems,$numratings) = $result->fields;
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'ratings' => $numratings);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
