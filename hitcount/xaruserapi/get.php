<?php

/**
 * get a hitcount for a specific item
 * @param $args['modname'] name of the module this hitcount is for
 * @param $args['itemtype'] item type of the item this hitcount is for
 * @param $args['objectid'] ID of the item this hitcount is for
 * @returns int
 * @return hits the corresponding hit count, or void if no hit exists
 */
function hitcount_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($modname)) ||
        (!isset($objectid))) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }
    if (empty($itemtype)) {
        $itemtype = 0;
    }

    // Security check
	if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:$itemtype:$objectid")) return;

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $hitcounttable = $xartable['hitcount'];

    // Get items
    $query = "SELECT xar_hits
            FROM $hitcounttable
            WHERE xar_moduleid = '" . xarVarPrepForStore($modid) . "'
              AND xar_itemtype = '" . xarVarPrepForStore($itemtype) . "'
              AND xar_itemid = '" . xarVarPrepForStore($objectid) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $hits = $result->fields[0];
    $result->close();

    return $hits;
}

?>