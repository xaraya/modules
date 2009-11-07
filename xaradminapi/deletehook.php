<?php
/**
 * Delete entry for a module item
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * delete entry for a module item - hook for ('item','delete','API')
 *
 * @param array $args with mandatory arguments:
 * - int   $args['objectid'] ID of the object
 * - array $args['extrainfo'] extra information
 * @return array updated extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @todo - actually raise errors, get intelligent and specific about cache files to remove
 */
function xarcachemanager_adminapi_deletehook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'deletehook', 'xarcachemanager');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
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
    $modid = xarMod::getRegId($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'deletehook', 'xarcachemanager');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
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
            // first, remove the corresponding block settings from the db
            $systemPrefix = xarDB::getPrefix();
            $blocksettings = $systemPrefix . '_cache_blocks';
            $dbconn = xarDB::getConn();
            $query = "SELECT nocache
                        FROM $blocksettings WHERE blockinstance_id = $objectid ";
            $result =& $dbconn->Execute($query);
            if (count($result) > 0) {
                $query = "DELETE FROM
                         $blocksettings WHERE blockinstance_id = $objectid ";
                $result =& $dbconn->Execute($query);
            }

            // blocks could be anywhere, we're not smart enough not know exactly where yet
            // so just flush all pages
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$pageCacheIsEnabled) {
                xarPageCache::flushCached('');
            }
            // and flush the block
        // FIXME: we can't filter on the middle of the key, only on the start of it
            $cacheKey = "-blockid" . $objectid;
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$blockCacheIsEnabled) {
                xarBlockCache::flushCached('');
            }
            break;
        case 'articles':
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$pageCacheIsEnabled) {
                xarPageCache::flushCached('articles-');
                // a status update might mean a new menulink and new base homepage
                xarPageCache::flushCached('base');
            }
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$blockCacheIsEnabled) {
                // a status update might mean a new menulink and new base homepage
                xarBlockCache::flushCached('base');
            }
            break;
        case 'privileges': // fall-through all modules that should flush the entire cache
        case 'roles':
            // if security changes, flush everything, just in case.
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$pageCacheIsEnabled) {
                xarPageCache::flushCached('');
            }
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$blockCacheIsEnabled) {
                xarBlockCache::flushCached('');
            }
            break;
        case 'dynamicdata':
            // get the objectname
            sys::import('modules.dynamicdata.class.objects.master');
            $objectinfo = DataObjectMaster::getObjectInfo(array('moduleid' => $modid,
                                                                'itemtype' => $itemtype));
        // CHECKME: how do we know if we need to e.g. flush dyn_example pages here ?
            // flush dynamicdata and objecturl pages
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$pageCacheIsEnabled) {
                xarPageCache::flushCached('dynamicdata-');
                if (!empty($objectinfo) && !empty($objectinfo['name'])) {
                    xarPageCache::flushCached('objecturl-' . $objectinfo['name'] . '-');
                }
            }
        // CHECKME: how do we know if we need to e.g. flush dyn_example module here ?
            // flush dynamicdata module
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$moduleCacheIsEnabled) {
                xarModuleCache::flushCached('dynamicdata-');
            }
            // flush objects by name, e.g. dyn_example
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$objectCacheIsEnabled) {
                if (!empty($objectinfo) && !empty($objectinfo['name'])) {
                    xarObjectCache::flushCached($objectinfo['name'] . '-');
                }
            }
            break;
        case 'autolinks': // fall-through all hooked utility modules that are admin modified
        case 'categories': // keep falling through
        case 'keywords': // keep falling through
        case 'html': // keep falling through
            // delete cachekey of each module autolinks is hooked to.
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$pageCacheIsEnabled) {
                $hooklist = xarMod::apiFunc('modules','admin','gethooklist');
                $modhooks = reset($hooklist[$modname]);

                foreach ($modhooks as $hookedmodname => $hookedmod) {
                    $cacheKey = "$hookedmodname-";
                    xarPageCache::flushCached($cacheKey);
                }
            }
            // no break because we want it to keep going and flush it's own cacheKey too
            // incase it's got a user view, like categories.
        // fall-through
        default:
            // identify pages that include the updated item and delete the cached files
            // nothing fancy yet, just flush it out
            $cacheKey = "$modname-";
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$pageCacheIsEnabled) {
                xarPageCache::flushCached($cacheKey);
            }
            // a deleted item might mean a menulink goes away
            if (xarCache::$outputCacheIsEnabled && xarOutputCache::$blockCacheIsEnabled) {
                xarBlockCache::flushCached('base-');
            }
            break;
    }

    if (xarCache::$outputCacheIsEnabled && xarModVars::get('xarcachemanager','AutoRegenSessionless')) {
        xarMod::apiFunc( 'xarcachemanager', 'admin', 'regenstatic');
    }

    // Return the extra info
    return $extrainfo;
}

?>
