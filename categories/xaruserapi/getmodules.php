<?php

/**
 * get the list of modules for which we're categorising items
 *
 * @returns array
 * @return $array[$modid] = $numitems
 */
function categories_userapi_getmodules($args)
{
    // Get arguments from argument array
    extract($args);

    // Security check
    if(!xarSecurityCheck('ViewCategoryLink')) return;

    if (empty($cid) || !is_numeric($cid)) {
        $cid = 0;
    }

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $categoriestable = $xartable['categories_linkage'];

    // Get items
    $sql = "SELECT xar_modid, COUNT(*)
            FROM $categoriestable";
    if (!empty($cid)) {
        $sql .= " WHERE xar_cid = " . xarVarPrepForStore($cid);
    }
    $sql .= " GROUP BY xar_modid";

    $result = $dbconn->Execute($sql);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$numitems) = $result->fields;
        $modlist[$modid] = $numitems;
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
