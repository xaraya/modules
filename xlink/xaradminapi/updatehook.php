<?php

/**
 * update entry for a module item - hook for ('item','update','API')
 * Optional $extrainfo['xlink_base'] and $extrainfo['xlink_id'] from arguments,
 * or 'xlink_base' and 'xlink_id' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xlink_adminapi_updatehook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object id', 'admin', 'updatehook', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'updatehook', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'updatehook', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    if (!empty($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'updatehook', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    // check if we need to save some reference id here
    if (isset($extrainfo['xlink_base'])) {
        $base = $extrainfo['xlink_base'];
    } else {
        $base = xarVarCleanFromInput('xlink_base');
    }
    if (!isset($base) || $base == '-') {
        return $extrainfo;
    }
    if (isset($extrainfo['xlink_id'])) {
        $refid = $extrainfo['xlink_id'];
    } else {
        $refid = xarVarCleanFromInput('xlink_id');
    }
    if (!isset($refid)) {
        return $extrainfo;
    }

    $basenames = array();
    if (!empty($itemtype)) {
        $getlist = xarModGetVar('xlink',$modname.'.'.$itemtype);
    } else {
        $getlist = xarModGetVar('xlink',$modname);
    }
    if (!isset($getlist)) {
        $getlist = xarModGetVar('xlink','default');
    }
    if (!empty($getlist)) {
        $basenames = split(',',$getlist);
    }
    if (count($basenames) > 0 && !in_array($base, $basenames)) {
        return $extrainfo;
    }

// TODO: re-evaluate having 1 or more references to the same module item
    $links = xarModAPIFunc('xlink','user','getlinks',
                           array('modid' => $modid,
                                 'itemtype' => $itemtype,
                                 'itemid' => $itemid));

    if (isset($links) && count($links) > 0) {
        foreach ($links as $link) {
            $oldbase = $link['basename'];
            $oldrefid = $link['refid'];
            break;
        }
        if (isset($oldbase) && isset($oldrefid) && $base == $oldbase && $refid == $oldrefid) {
            $extrainfo['xlink_base'] = $base;
            $extrainfo['xlink_id'] = $refid;

            return $extrainfo;
        }
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xlinktable = $xartable['xlink'];

// TODO: re-evaluate having 1 or more references to the same module item
    if (isset($oldbase) && isset($oldrefid)) {
        // Delete old link(s) for this module item
        $query = "DELETE FROM $xlinktable
                  WHERE xar_moduleid = " . xarVarPrepForStore($modid) . "
                    AND xar_itemtype = " . xarVarPrepForStore($itemtype) . "
                    AND xar_itemid = " . xarVarPrepForStore($itemid) . "
                    AND xar_basename = '" . xarVarPrepForStore($oldbase) . "'
                    AND xar_refid = '" . xarVarPrepForStore($oldrefid) . "'";

        $result =& $dbconn->Execute($query);
        if (!$result) {
            // we *must* return $extrainfo for now, or the next hook will fail
            //return false;
            return $extrainfo;
        }
    }

// TODO: generate auto-increment per base if necessary

    // Get a new xlink ID
    $nextId = $dbconn->GenId($xlinktable);
    // Create new xlink
    $query = "INSERT INTO $xlinktable (xar_id,
                                       xar_basename,
                                       xar_refid,
                                       xar_moduleid,
                                       xar_itemtype,
                                       xar_itemid)
            VALUES ($nextId,
                    '" . xarVarPrepForStore($base) . "',
                    '" . xarVarPrepForStore($refid) . "',
                    '" . xarVarPrepForStore($modid) . "',
                    '" . xarVarPrepForStore($itemtype) . "',
                    '" . xarVarPrepForStore($objectid) . "')";

    $result =& $dbconn->Execute($query);
    if (!$result) {
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    $xlinkid = $dbconn->PO_Insert_ID($xlinktable, 'xar_id');

    $extrainfo['xlink_base'] = $base;
    $extrainfo['xlink_id'] = $refid;

    // Return the extra info
    return $extrainfo;
}


?>
