<?php

/**
 * delete a hitcount item - hook for ('item','delete','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @param $args['modname'] name of the calling module (not used in hook calls)
 * @param $args['itemtype'] optional item type for the item (not used in hook calls)
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function hitcount_adminapi_delete($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'delete', 'Hitcount');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // When called via hooks, modname wil be empty, but we get it from the
    // current module
    if (empty($modname)) {
        $modname = xarModGetName();
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'delete', 'Hitcount');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

// TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
	if(!xarSecurityCheck('DeleteHitcountItem',1,'Item',"$modname:$itemtype:$objectid")) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $hitcounttable = $xartable['hitcount'];

    // Don't bother looking if the item exists here...
    $query = "DELETE FROM $hitcounttable
            WHERE xar_moduleid = '" . xarVarPrepForStore($modid) . "'
              AND xar_itemtype = '" . xarVarPrepForStore($itemtype) . "'
              AND xar_itemid = '" . xarVarPrepForStore($objectid) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModCallHooks('item', 'delete', $exid, '');

    // Return the extra info
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
    return $extrainfo;
}

?>