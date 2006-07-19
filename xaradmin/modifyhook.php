<?php
/**
 * Modify hook
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * modify an entry for a module item - hook for ('item','modify','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return string hook output in HTML
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarcachemanager_admin_modifyhook($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminXarCache', 0)) { return ''; }

    // If we are disabled, nothing to do here
    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    $outputCacheDir = $varCacheDir . '/output';
    if (!file_exists($outputCacheDir.'/cache.touch')) {return '';}


    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'modifyhook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'modifyhook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
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
    if ($modname !== 'blocks') {
        return '';
    }
    // only display config hooks if block level output caching has been enabled
    // (check for the file rather than the constant so config options can be tweaked
    //  even when output caching has been temporarily disabled)
    if (!file_exists(xarCoreGetVarDirPath() . '/cache/output/cache.blocklevel')) {
        return '';
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'modifyhook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
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
    if ($result && !$result->EOF) {
        list ($noCache, $pageShared, $userShared, $blockCacheExpireTime) = $result->fields;
    } else {
        $noCache = 0;
        $pageShared = 0;
        $userShared = 0;
        $blockCacheExpireTime = null;
        // Get the instance details.
        $instance = xarModAPIfunc('blocks', 'user', 'get', array('bid' => $itemid));
        // Try loading some defaults from the block init array (cfr. articles/random)
        if (!empty($instance) && !empty($instance['module']) && !empty($instance['type'])) {
            $initresult = xarModAPIfunc('blocks', 'user', 'read_type_init',
                                        array('module' => $instance['module'],
                                              'type' => $instance['type']));
            if (!empty($initresult) && is_array($initresult)) {
                if (isset($initresult['nocache'])) {
                    $noCache = $initresult['nocache'];
                }
                if (isset($initresult['pageshared'])) {
                    $pageShared = $initresult['pageshared'];
                }
                if (isset($initresult['usershared'])) {
                    $userShared = $initresult['usershared'];
                }
                if (isset($initresult['cacheexpire'])) {
                    $blockCacheExpireTime = $initresult['cacheexpire'];
                }
            }
        }
    }
    if (!empty($blockCacheExpireTime)) {
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
