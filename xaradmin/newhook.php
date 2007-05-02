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
function trackback_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $extrainfo;
    }

    if (!isset($objectid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $extrainfo;
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

    if (isset($extrainfo['tb_pingurl'])) {
        $pingurl = $extrainfo['tb_pingurl'];
    } else {
        if (!xarVarFetch('tb_pingurl', 'str:1:', $pingurl, '', XARVAR_NOT_REQUIRED)) return;
    }
    if (empty($pingurl)) {
        $pingurl = '';
    }

    if (isset($extrainfo['tb_excerpt'])) {
        $excerpt = $extrainfo['tb_excerpt'];
    } else {
        if (!xarVarFetch('tb_excerpt', 'str:1:', $excerpt, '', XARVAR_NOT_REQUIRED)) return;
    }
    if (empty($excerpt)) {
        $excerpt = '';
    }

    return xarTplModule('trackback','admin','newhook',
                        array('pingurl' => $pingurl,
                              'excerpt' => $excerpt));
}

?>