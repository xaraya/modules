<?php

/**
 * update a hitcount item - used by display hook hitcount_user_display
 *
 * @param $args['modname'] name of the calling module (see _user_display)
 * @param $args['itemtype'] optional item type for the item (or in extrainfo)
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] may contain itemtype
 * @param $args['hits'] (optional) hit count for the item
 * @returns int
 * @return the new hitcount for this item, or void on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function hitcount_adminapi_update($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'update', 'Hitcount');
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
                    'module name', 'admin', 'update', 'Hitcount');
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
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
	if(!xarSecurityCheck('ReadHitcountItem',1,'Item',"$modname:$itemtype:$objectid")) return;

    if (!xarModAPILoad('hitcount', 'user')) return;

    // get current hit count
    $oldhits = xarModAPIFunc('hitcount',
                            'user',
                            'get',
                            array('objectid' => $objectid,
                                  'itemtype' => $itemtype,
                                  'modname' => $modname));

    // create the item if necessary
    if (!isset($oldhits)) {
        $hcid = xarModAPIFunc('hitcount','admin','create',
                             array('objectid' => $objectid,
                                   'itemtype' => $itemtype,
                                   'modname' => $modname));
        if (!isset($hcid)) {
            return; // throw back whatever it was that failed
        }
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $hitcounttable = $xartable['hitcount'];

    // set to the new hit count
    if (!empty($hits) && is_numeric($hits)) {
        $query = "UPDATE $hitcounttable
                SET xar_hits = '" . xarVarPrepForStore($hits) . "'
                WHERE xar_moduleid = '" . xarVarPrepForStore($modid) . "'
                  AND xar_itemtype = '" . xarVarPrepForStore($itemtype) . "'
                  AND xar_itemid = '" . xarVarPrepForStore($objectid) . "'";
    } else {
        $query = "UPDATE $hitcounttable
                SET xar_hits = xar_hits + 1
                WHERE xar_moduleid = '" . xarVarPrepForStore($modid) . "'
                  AND xar_itemtype = '" . xarVarPrepForStore($itemtype) . "'
                  AND xar_itemid = '" . xarVarPrepForStore($objectid) . "'";
        $hits = $oldhits + 1;
    }
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Return the new hitcount (give or take a few other hits in the meantime)
    return $hits;
}

?>
