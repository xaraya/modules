<?php

/**
 * modify an entry for a module item - hook for ('item','new','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns string
 * @return hook output in HTML
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xlink_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'newhook', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'newhook', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
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
                    'module name', 'admin', 'newhook', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }
    if (empty($itemid)) {
        $itemid = 0;
    }

    if (isset($extrainfo['xlink_base'])) {
        $base = $extrainfo['xlink_base'];
    } else {
        $base = xarVarCleanFromInput('xlink_base');
    }
    if (empty($base)) {
        $base = '';
    }
    if (isset($extrainfo['xlink_id'])) {
        $refid = $extrainfo['xlink_id'];
    } else {
        $refid = xarVarCleanFromInput('xlink_id');
    }
    if (empty($refid)) {
        $refid = '';
    }

    $basenames = array();
    if (!empty($itemtype)) {
        $list = xarModGetVar('xlink',"$modname.$itemtype");
    } else {
        $list = xarModGetVar('xlink',$modname);
    }
    if (empty($list)) {
        $list = xarModGetVar('xlink','default');
    }
    if (!empty($list)) {
        $basenames = explode(',',$list);
    }

    return xarTplModule('xlink','admin','newhook',
                        array('basenames' => $basenames,
                              'base' => $base,
                              'id' => $refid));
}

?>
