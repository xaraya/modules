<?php
/**
 * Get the list of modules for which we're counting items
 *
 * @return array $array[$modid] = $numitems
 */
function trackback_userapi_getmodules()
{
    // Security check
    if (!xarSecurityCheck('ViewTrackBack')) return;

    // Database information
    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    // Get items
    $query = "SELECT moduleid, COUNT(itemid)
            FROM $trackBackTable
            GROUP BY moduleid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modList = array();
    while (!$result->EOF) {
        list($modId,$numItems) = $result->fields;
        $modList[$modId] = $numItems;
        $result->MoveNext();
    }
    $result->close();

    return $modList;
}
?>