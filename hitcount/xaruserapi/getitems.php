<?php

/**
 * get a hitcount for a list of items
 * @param $args['modname'] name of the module you want items from
 * @param $args['itemtype'] item type of the items (only 1 type supported per call)
 * @param $args['itemids'] array of item IDs
 * @returns array
 * @return $array[$itemid] = $hits;
 */
function hitcount_userapi_getitems($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($modname)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }
    if (empty($itemtype)) {
        $itemtype = 0;
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
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
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
    $result =& $dbconn->Execute($query);
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