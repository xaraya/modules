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

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'user', 'get', 'Hitcount');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // When called via hooks, modname wil be empty, but we get it from the
    // current module
    if (empty($modname)) {
        $modname = xarModGetName();
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'user', 'get', 'Hitcount');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo) && is_array($extrainfo) &&
             isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

// TODO: re-evaluate this for hook calls !!
    // Security check
	if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:$itemtype:$objectid")) return;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
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
