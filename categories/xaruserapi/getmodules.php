<?php

/**
 * get the list of modules and itemtypes for which we're categorising items
 *
 * @returns array
 * @return $array[$modid][$itemtype] = array('items' => $numitems,'cats' => $numcats,'links' => $numlinks);
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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categoriestable = $xartable['categories_linkage'];

    // Get items
    $sql = "SELECT xar_modid, xar_itemtype, COUNT(*), COUNT(DISTINCT xar_iid), COUNT(DISTINCT xar_cid)
            FROM $categoriestable";
    if (!empty($cid)) {
        $sql .= " WHERE xar_cid = " . xarVarPrepForStore($cid);
    }
    $sql .= " GROUP BY xar_modid, xar_itemtype";

    $result = $dbconn->Execute($sql);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numlinks,$numitems,$numcats) = $result->fields;
        if (!isset($modlist[$modid])) {
            $modlist[$modid] = array();
        }
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'cats' => $numcats, 'links' => $numlinks);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
