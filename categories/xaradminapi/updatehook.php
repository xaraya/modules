<?php

/**
 * update linkage for an item - hook for ('item','update','API')
 * Needs $extrainfo['cids'] from arguments, or 'cids' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function categories_adminapi_updatehook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'createhook', 'categories');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
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
                    'module name', 'admin', 'createhook', 'categories');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // see what we have to do here (might be empty => we need to unlink)
    if (empty($extrainfo['cids'])) {
        // try to get cids from input
        $cids = xarVarCleanFromInput('cids');
        if (empty($cids) || !is_array($cids)) {
            $extrainfo['cids'] = array();
        } else {
            $extrainfo['cids'] = $cids;
        }
    }
    // get all valid cids for this item
    // Note : an item may *not* belong to the same cid twice
    $seencid = array();
    foreach ($extrainfo['cids'] as $cid) {
        if (empty($cid) || !is_numeric($cid)) {
            continue;
        }
        $seencid[$cid] = 1;
    }
    $cids = array_keys($seencid);

    if (count($cids) == 0) {
        if (!xarModAPIFunc('categories', 'admin', 'unlink',
                          array('iid' => $objectid,
                                'modid' => $modid))) {
            return false;
        }
    } elseif (!xarModAPIFunc('categories', 'admin', 'linkcat',
                            array('cids'  => $cids,
                                  'iids'  => array($objectid),
                                  'modid' => $modid,
                                  'clean_first' => true))) {
        return false;
    }

    // Return the extra info
    return $extrainfo;
}

?>