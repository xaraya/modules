<?php

/**
 * get the list of modules where we're tracking item changes
 *
 * @returns array
 * @return $array[$modid][$itemtype] = array('items' => $numitems,'changes' => $numchanges);
 */
function changelog_userapi_getmodules($args)
{
// Security Check
   if (!xarSecurityCheck('ReadChangeLog')) return;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $changelogtable = $xartable['changelog'];

    // Get items
    $query = "SELECT xar_moduleid, xar_itemtype, COUNT(DISTINCT xar_itemid), COUNT(*)
            FROM $changelogtable
            GROUP BY xar_moduleid, xar_itemtype";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numitems,$numchanges) = $result->fields;
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'changes' => $numchanges);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
