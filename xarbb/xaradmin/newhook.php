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
function xarbb_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'admin', 'newhook', 'xarbb');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'admin', 'newhook', 'xarbb');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module name', 'admin', 'newhook', 'xarbb');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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

    $data['items'] = xarModAPIFunc('xarbb',
                                   'user',
                                   'getallforums');

    if (isset($extrainfo['xarbb_forum'])) {
        $data['default'] = $extrainfo['xarbb_forum'];
    } else {
        $data['default'] = xarVarCleanFromInput('xarbb_forum');
    }
    if (empty($forum)) {
        $data['default'] = '';
    }
    return $data;
}
?>