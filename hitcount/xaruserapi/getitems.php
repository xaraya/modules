<?php

/**
 * get a hitcount for a list of items
 * @param $args['modname'] name of the module you want items from, or
 * @param $args['modid'] module id you want items from
 * @param $args['itemtype'] item type of the items (only 1 type supported per call)
 * @param $args['itemids'] array of item IDs
 * @param $args['sort'] string sort by itemid (default) or numhits
 * @param $args['numitems'] number of items to return
 * @param $args['startnum'] start at this number (1-based)
 * @returns array
 * @return $array[$itemid] = $hits;
 */
function hitcount_userapi_getitems($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($modname) && !isset($modid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }
    if (!empty($modname)) {
        $modid = xarModGetIDFromName($modname);
    }
    if (empty($modid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    } elseif (empty($modname)) {
        $modinfo = xarModGetInfo($modid);
        $modname = $modinfo['name'];
    }
    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (empty($sort)) {
        $sort = 'itemid';
    }
    if (empty($startnum)) {
        $startnum = 1;
    }

    if (!isset($itemids)) {
        $itemids = array();
    }

    // Security check
    if (count($itemids) > 0) {
        foreach ($itemids as $itemid) {
			if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:$itemtype:$objectid")) return;
        }
    } else {
			if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:$itemtype:All")) return;
    }

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $hitcounttable = $xartable['hitcount'];

    // Get items
    $query = "SELECT xar_itemid, xar_hits
            FROM $hitcounttable
            WHERE xar_moduleid = '" . xarVarPrepForStore($modid) . "'
              AND xar_itemtype = '" . xarVarPrepForStore($itemtype) . "'";
    if (count($itemids) > 0) {
        $allids = join(', ',$itemids);
        $query .= " AND xar_itemid IN ('" . xarVarPrepForStore($allids) . "')";
    }
    if ($sort == 'numhits') {
        $query .= " ORDER BY xar_hits DESC, xar_itemid ASC";
    } else {
        $query .= " ORDER BY xar_itemid ASC";
    }

    if (!empty($numitems) && !empty($startnum)) {
        $result = $dbconn->SelectLimit($query, $numitems, $startnum - 1);
    } else {
        $result =& $dbconn->Execute($query);
    }
    if (!$result) return;

    $hitlist = array();
    while (!$result->EOF) {
        list($id,$hits) = $result->fields;
        $hitlist[$id] = $hits;
        $result->MoveNext();
    }
    $result->close();

    return $hitlist;
}

?>
