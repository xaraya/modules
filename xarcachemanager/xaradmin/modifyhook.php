<?php

/**
 * modify an entry for a module item - hook for ('item','modify','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns string
 * @return hook output in HTML
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarcachemanager_admin_modifyhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'modifyhook', 'changelog');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'modifyhook', 'changelog');
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
    
    // we are only interested in the config of block output caching for now
    if (($modname !== 'blocks') || !xarModGetVar('xarcachemanager','CacheBlockOutput')) {
        return '';
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'modifyhook', 'changelog');
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

    $systemPrefix = xarDBGetSystemTablePrefix();
    $blocksettings = $systemPrefix . '_cache_blocks';
    $dbconn =& xarDBGetConn();
    $query = "SELECT xar_nocache,
             xar_page,
             xar_user,
             xar_expire
             FROM $blocksettings WHERE xar_bid = $itemid ";
    $result =& $dbconn->Execute($query);
    if ($result) {
        list ($noCache, $pageShared, $userShared, $blockCacheExpireTime) = $result->fields;
    }
    if ($blockCacheExpireTime > 0 ) {
        $blockCacheExpireTime = xarModAPIFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                               array('starttime' => $blockCacheExpireTime,
                                                     'direction' => 'from'));
    }

    return xarTplModule('xarcachemanager','admin','modifyhook',
                        array('noCache' => $noCache,
                              'pageShared' => $pageShared,
                              'userShared' => $userShared,
                              'cacheExpire' => $blockCacheExpireTime));
}

?>
