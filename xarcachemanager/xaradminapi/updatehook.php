<?php

/**
 * update entry for a module item - hook for ('item','update','API')
 * Optional $extrainfo['xarcachemanager_remark'] from arguments, or 'xarcachemanager_remark' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * todo - actually raise errors, get intelligent and specific about cache files to remove
 */
function xarcachemanager_adminapi_updatehook($args)
{
    extract($args);

    $outputCacheDir = xarCoreGetVarDirPath() . '/cache/output/';

    if (!file_exists($outputCacheDir . 'cache.touch')) {
        // caching is not enabled and xarCache will not be available
        return;
    }

    if (!function_exists('xarPageFlushCached')) {
        // caching is on, but the function isn't available
        // load xarCache to make it so
        include 'includes/xarCache.php';
        if (xarCache_init(array('cacheDir' => $outputCacheDir)) == false) {
            // somethings wrong, caching should be off now
            return;
        }
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'updatehook', 'xarcachemanager');
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
                    'module name', 'admin', 'updatehook', 'xarcachemanager');
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

    // TODO: make all the module cache flushing behavior admin configurable

    switch($modname) {
        case 'blocks':
            // first, save the new settings
            xarVarFetch('nocache', 'isset', $nocache, 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('pageshared', 'isset', $pageshared, 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('usershared', 'isset', $usershared, 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('cacheexpire', 'str:1:9', $cacheexpire, NULL, XARVAR_NOT_REQUIRED);

            if (empty($nocache)) {
                $nocache = 0;
            }
            if (empty($pageshared)) {
                $pageshared = 0;
            }
            if (!isset($cacheexpire)) {
                $cacheexpire = 'NULL';
            }
            if ($cacheexpire > 0 ) {
                $cacheexpire = xarModAPIFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                              array('starttime' => $cacheexpire,
                                                    'direction' => 'to'));
            }

            $systemPrefix = xarDBGetSystemTablePrefix();
            $blocksettings = $systemPrefix . '_cache_blocks';
            $dbconn =& xarDBGetConn();
            $query = "SELECT xar_nocache
                        FROM $blocksettings WHERE xar_bid = $objectid ";
            $result =& $dbconn->Execute($query);
            if (count($result) > 0) {
                $query = "DELETE FROM
                         $blocksettings WHERE xar_bid = $objectid ";
                $result =& $dbconn->Execute($query);
            }
            $query = "INSERT INTO $blocksettings (xar_bid,
                                                  xar_nocache,
                                                  xar_page,
                                                  xar_user,
                                                  xar_expire)
                        VALUES (" . xarVarPrepForStore($objectid) . ",
                                " . xarVarPrepForStore($nocache) . ",
                                " . xarVarPrepForStore($pageshared) . ",
                                " . xarVarPrepForStore($usershared) . ",
                                " . xarVarPrepForStore($cacheexpire) . ")";
            $result =& $dbconn->Execute($query);
            
            // blocks could be anywhere, we're not smart enough not know exactly where yet
            // so just flush all pages
            $cacheKey = "-user-";
            xarPageFlushCached($cacheKey);
            // and flush the block
            $cacheKey = "-blockid" . $objectid;
            xarPageFlushCached($cacheKey);
            break;
        case 'privileges': // fall-through all modules that should flush the entire cache
        case 'roles':
            // if security changes, flush everything, just in case.
            $cacheKey = "";
            xarPageFlushCached($cacheKey);
            break;
        case 'autolinks': // fall-through all hooked utility modules that are admin modified
        case 'categories': // keep falling through
        case 'keywords': // keep falling through
        case 'html': // keep falling through
            // delete cachekey of each module autolinks is hooked to.
            $hooklist = xarModAPIFunc('modules','admin','gethooklist');
            $modhooks = reset($hooklist[$modname]);

            foreach ($modhooks as $hookedmodname => $hookedmod) {
                $cacheKey = "$hookedmodname-";
                xarPageFlushCached($cacheKey);
            }
            // no break because we want it to keep going and flush it's own cacheKey too
            // incase it's got a user view, like categories.
        case 'articles': // fall-through
            //nothing special yet
        default:
            // identify pages that include the updated item and delete the cached files
            // nothing fancy yet, just flush it out
            $cacheKey = "$modname-";
            xarPageFlushCached($cacheKey);
            break;
    }

    // Return the extra info
    return $extrainfo;
}

?>
