<?php

/**
 * create an entry for a module item - hook for ('item','create','GUI')
 * Optional $extrainfo['xlink_base'] and $extrainfo['xlink_id'] from arguments,
 * or 'xlink_base' and 'xlink_id' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns array
 * @return extrainfo array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xlink_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'createhook', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, modname wil be empty, but we get it from the
    // extrainfo or the current module
    if (empty($modname)) {
        if (!empty($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarModGetName();
        }
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'createhook', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xlinktable = $xartable['xlink'];

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

    return $extrainfo;
}

?>
