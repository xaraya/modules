<?php
/**
 * Classes to handle cache hooks
 *
 * @package modules\xarcachemanager
 * @subpackage xarcachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/
sys::import('modules.xarcachemanager.class.cache_manager');

class xarCache_Hooks extends xarObject
{
    public static function init(array $args = [])
    {
    }

    /**
     * regenerate the page output cache of URLs in the session-less list
     * @author jsb
     *
     * @return void
     */
    public static function regenstatic($nolimit = null)
    {
        $urls = [];
        $outputCacheDir = sys::varpath() . '/cache/output/';

        // make sure output caching is really enabled, and that we are caching pages
        if (!xarCache::$outputCacheIsEnabled || !xarOutputCache::$pageCacheIsEnabled) {
            return;
        }

        // flush the static pages
        xarPageCache::flushCached('static');

        $configKeys = ['Page.SessionLess'];
        $sessionlessurls = xarCache_Manager::get_config(
            ['keys' => $configKeys, 'from' => 'file', 'viahook' => true]
        );

        $urls = $sessionlessurls['Page.SessionLess'];

        if (!$nolimit) {
            // randomize the order of the urls just in case the timelimit cuts the
            // process short - no need to always drop the same pages.
            shuffle($urls);

            // set a time limit for the regeneration
            // TODO: make the timelimit variable and configurable.
            $timelimit = time() + 10;
        }

        foreach ($urls as $url) {
            // Make sure the url isn't empty before calling getfile()
            if (strlen(trim($url))) {
                xarMod::apiFunc('base', 'user', 'getfile', ['url' => $url, 'superrors' => true]);
            }
            if (!$nolimit && time() > $timelimit) {
                break;
            }
        }

        return;
    }

    /**
     * flush the appropriate cache when a module item is created- hook for ('item','create','API')
     *
     * @param array $args with mandatory arguments:
     * - int   $args['objectid'] ID of the object
     * - array $args['extrainfo'] extra information
     * @return array updated extrainfo array
     * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
     * @todo - actually raise errors, get intelligent and specific about cache files to remove
     */
    public static function createhook($args)
    {
        extract($args);

        if (!isset($objectid) || !is_numeric($objectid)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'object ID',
                'admin',
                'createhook',
                'xarcachemanager'
            );
            throw new Exception($msg);
            return;
        }
        if (!isset($extrainfo) || !is_array($extrainfo)) {
            $extrainfo = [];
        }

        if (!xarCache::$outputCacheIsEnabled) {
            // nothing more to do here
            return $extrainfo;
        }

        // When called via hooks, modname wil be empty, but we get it from the
        // extrainfo or the current module
        if (empty($modname)) {
            if (!empty($extrainfo['module'])) {
                $modname = $extrainfo['module'];
            } else {
                $modname = xarMod::getName();
            }
        }
        $modid = xarMod::getRegId($modname);
        if (empty($modid)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'module name',
                'admin',
                'createhook',
                'xarcachemanager'
            );
            throw new Exception($msg);
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

        switch ($modname) {
            case 'blocks':
                // blocks could be anywhere, we're not smart enough not know exactly where yet
                // so just flush all pages
                if (xarOutputCache::$pageCacheIsEnabled) {
                    xarPageCache::flushCached('');
                }
                break;
            case 'privileges': // fall-through all modules that should flush the entire cache
            case 'roles':
                // if security changes, flush everything, just in case.
                if (xarOutputCache::$pageCacheIsEnabled) {
                    xarPageCache::flushCached('');
                }
                if (xarOutputCache::$blockCacheIsEnabled) {
                    xarBlockCache::flushCached('');
                }
                break;
            case 'articles':
                if (isset($extrainfo['status']) && $extrainfo['status'] == 0) {
                    break;
                }
                if (xarOutputCache::$pageCacheIsEnabled) {
                    xarPageCache::flushCached('articles-');
                    // a status update might mean a new menulink and new base homepage
                    xarPageCache::flushCached('base');
                }
                if (xarOutputCache::$blockCacheIsEnabled) {
                    // a status update might mean a new menulink and new base homepage
                    xarBlockCache::flushCached('base');
                }
                break;
            case 'dynamicdata':
                // get the objectname
                sys::import('modules.dynamicdata.class.objects.descriptor');
                $objectinfo = DataObjectDescriptor::getObjectID(['moduleid'  => $modid,
                                                                 'itemtype' => $itemtype, ]);
                // CHECKME: how do we know if we need to e.g. flush dyn_example pages here ?
                // flush dynamicdata and objecturl pages
                if (xarOutputCache::$pageCacheIsEnabled) {
                    xarPageCache::flushCached('dynamicdata-');
                    if (!empty($objectinfo) && !empty($objectinfo['name'])) {
                        xarPageCache::flushCached('objecturl-' . $objectinfo['name'] . '-');
                    }
                }
                // CHECKME: how do we know if we need to e.g. flush dyn_example module here ?
                // flush dynamicdata module
                if (xarOutputCache::$moduleCacheIsEnabled) {
                    xarModuleCache::flushCached('dynamicdata-');
                }
                // flush objects by name, e.g. dyn_example
                if (xarOutputCache::$objectCacheIsEnabled) {
                    if (!empty($objectinfo) && !empty($objectinfo['name'])) {
                        xarObjectCache::flushCached($objectinfo['name'] . '-');
                    }
                }
                break;
            case 'autolinks': // fall-through all hooked utility modules that are admin modified
            case 'categories': // keep falling through
            case 'html': // keep falling through
            case 'keywords': // keep falling through
                // delete cachekey of each module autolinks is hooked to.
                if (xarOutputCache::$pageCacheIsEnabled) {
                    $hooklist = xarMod::apiFunc('modules', 'admin', 'gethooklist');
                    $modhooks = reset($hooklist[$modname]);

                    foreach ($modhooks as $hookedmodname => $hookedmod) {
                        $cacheKey = "$hookedmodname-";
                        xarPageCache::flushCached($cacheKey);
                    }
                }
                // incase it's got a user view, like categories.
                // no break
            default:
                // identify pages that include the updated item and delete the cached files
                // nothing fancy yet, just flush it out
                $cacheKey = "$modname-";
                if (xarOutputCache::$pageCacheIsEnabled) {
                    xarPageCache::flushCached($cacheKey);
                }
                // a new item might mean a new menulink
                if (xarOutputCache::$blockCacheIsEnabled) {
                    xarBlockCache::flushCached('base-');
                }
                break;
        }

        if (xarModVars::get('xarcachemanager', 'AutoRegenSessionless')) {
            self::regenstatic();
        }

        return $extrainfo;
    }

    /**
     * modify an entry for a module item - hook for ('item','modify','GUI')
     *
     * @param array $args with mandatory arguments:
     * - int   $args['objectid'] ID of the object
     * - array $args['extrainfo'] extra information
     * @return string hook output in HTML
     * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
     */
    public static function modifyhook($args)
    {
        extract($args);

        if (!xarSecurity::check('AdminXarCache', 0)) {
            return '';
        }

        // Get the output cache directory so you can access it even if output caching is disabled
        $outputCacheDir = xarCache::getOutputCacheDir();

        // only display modify hooks if block level output caching has been enabled
        // (don't check if output caching is enabled here so config options can be tweaked
        //  even when output caching has been temporarily disabled)
        if (!xarOutputCache::$blockCacheIsEnabled) {
            return '';
        }

        if (!isset($extrainfo)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'extrainfo',
                'admin',
                'modifyhook',
                'changelog'
            );
            throw new Exception($msg);
            return $msg;
        }

        if (!isset($objectid) || !is_numeric($objectid)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'object ID',
                'admin',
                'modifyhook',
                'changelog'
            );
            throw new Exception($msg);
            return $msg;
        }

        // When called via hooks, the module name may be empty, so we get it from
        // the current module
        if (empty($extrainfo['module'])) {
            $modname = xarMod::getName();
        } else {
            $modname = $extrainfo['module'];
        }

        // we are only interested in the config of block output caching for now
        if ($modname !== 'blocks') {
            return '';
        }

        $modid = xarMod::getRegId($modname);
        if (empty($modid)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'module name',
                'admin',
                'modifyhook',
                'changelog'
            );
            throw new Exception($msg);
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

        $systemPrefix = xarDB::getPrefix();
        $blocksettings = $systemPrefix . '_cache_blocks';
        $dbconn = xarDB::getConn();
        $query = "SELECT nocache,
                 page,
                 theuser,
                 expire
                 FROM $blocksettings WHERE blockinstance_id = $itemid ";
        $result =& $dbconn->Execute($query);
        if ($result && !$result->EOF) {
            [$noCache, $pageShared, $userShared, $blockCacheExpireTime] = $result->fields;
        } else {
            $noCache = 0;
            $pageShared = 0;
            $userShared = 0;
            $blockCacheExpireTime = null;
            // Get the instance details.
            $instance = xarMod::apiFunc('blocks', 'user', 'get', ['bid' => $itemid]);
            // Try loading some defaults from the block init array (cfr. articles/random)
            if (!empty($instance) && !empty($instance['module']) && !empty($instance['type'])) {
                $initresult = xarMod::apiFunc(
                    'blocks',
                    'user',
                    'read_type_init',
                    ['module' => $instance['module'],
                                                  'type' => $instance['type'], ]
                );
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
            $blockCacheExpireTime = xarCache_Manager::convertseconds(
                ['starttime' => $blockCacheExpireTime,
                                                         'direction' => 'from', ]
            );
        }
        return xarTpl::module(
            'xarcachemanager',
            'admin',
            'modifyhook',
            ['noCache' => $noCache,
                                  'pageShared' => $pageShared,
                                  'userShared' => $userShared,
                                  'cacheExpire' => $blockCacheExpireTime, ]
        );
    }

    /**
     * update entry for a module item - hook for ('item','update','API')
     * Optional $extrainfo['xarcachemanager_remark'] from arguments, or 'xarcachemanager_remark' from input
     *
     * @param array $args with mandatory arguments:
     * - int   $args['objectid'] ID of the object
     * - array $args['extrainfo'] extra information
     * @return array updated extrainfo array
     * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
     * @todo - actually raise errors, get intelligent and specific about cache files to remove
     */
    public static function updatehook($args)
    {
        extract($args);

        if (!isset($objectid) || !is_numeric($objectid)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'object ID',
                'admin',
                'updatehook',
                'xarcachemanager'
            );
            throw new Exception($msg);
            return;
        }
        if (!isset($extrainfo) || !is_array($extrainfo)) {
            $extrainfo = [];
        }

        // When called via hooks, modname wil be empty, but we get it from the
        // extrainfo or the current module
        if (empty($modname)) {
            if (!empty($extrainfo['module'])) {
                $modname = $extrainfo['module'];
            } else {
                $modname = xarMod::getName();
            }
        }
        $modid = xarMod::getRegId($modname);
        if (empty($modid)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'module name',
                'admin',
                'updatehook',
                'xarcachemanager'
            );
            throw new Exception($msg);
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

        switch ($modname) {
            case 'blocks':
                // Get the output cache directory so you can access it even if output caching is disabled
                $outputCacheDir = xarCache::getOutputCacheDir();

                // first, if authorized, save the new settings
                // (don't check if output caching is enabled here so config options can be tweaked
                //  even when output caching has been temporarily disabled)
                if (xarOutputCache::$blockCacheIsEnabled &&
                    xarSecurity::check('AdminXarCache', 0)) {
                    xarVar::fetch('nocache', 'isset', $nocache, 0, xarVar::NOT_REQUIRED);
                    xarVar::fetch('pageshared', 'isset', $pageshared, 0, xarVar::NOT_REQUIRED);
                    xarVar::fetch('usershared', 'isset', $usershared, 0, xarVar::NOT_REQUIRED);
                    xarVar::fetch('cacheexpire', 'str:1:9', $cacheexpire, null, xarVar::NOT_REQUIRED);

                    if (empty($nocache)) {
                        $nocache = 0;
                    }
                    if (empty($pageshared)) {
                        $pageshared = 0;
                    }
                    if (!isset($cacheexpire)) {
                        $cacheexpire = null;
                    }
                    if (!empty($cacheexpire)) {
                        $cacheexpire = xarCache_Manager::convertseconds(
                            ['starttime' => $cacheexpire,
                                  'direction' => 'to', ]
                        );
                    }

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
                    $query = "INSERT INTO $blocksettings (blockinstance_id,
                                                          nocache,
                                                          page,
                                                          theuser,
                                                          expire)
                                VALUES (?,?,?,?,?)";
                    $bindvars = [$objectid, $nocache, $pageshared, $usershared, $cacheexpire];
                    $result =& $dbconn->Execute($query, $bindvars);
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
                sys::import('modules.dynamicdata.class.objects.descriptor');
                $objectinfo = DataObjectDescriptor::getObjectID(['moduleid'  => $modid,
                                                                 'itemtype' => $itemtype, ]);
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
                    $hooklist = xarMod::apiFunc('modules', 'admin', 'gethooklist');
                    $modhooks = reset($hooklist[$modname]);

                    foreach ($modhooks as $hookedmodname => $hookedmod) {
                        $cacheKey = "$hookedmodname-";
                        xarPageCache::flushCached($cacheKey);
                    }
                }
                // incase it's got a user view, like categories.
                // no break
            default:
                // identify pages that include the updated item and delete the cached files
                // nothing fancy yet, just flush it out
                $cacheKey = "$modname-";
                if (xarCache::$outputCacheIsEnabled && xarOutputCache::$pageCacheIsEnabled) {
                    xarPageCache::flushCached($cacheKey);
                }
                break;
        }

        if (xarCache::$outputCacheIsEnabled && xarModVars::get('xarcachemanager', 'AutoRegenSessionless')) {
            self::regenstatic();
        }

        // Return the extra info
        return $extrainfo;
    }

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
    public static function deletehook($args)
    {
        extract($args);

        if (!isset($objectid) || !is_numeric($objectid)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'object ID',
                'admin',
                'deletehook',
                'xarcachemanager'
            );
            throw new Exception($msg);
            return;
        }
        if (!isset($extrainfo) || !is_array($extrainfo)) {
            $extrainfo = [];
        }

        // When called via hooks, modname wil be empty, but we get it from the
        // extrainfo or the current module
        if (empty($modname)) {
            if (!empty($extrainfo['module'])) {
                $modname = $extrainfo['module'];
            } else {
                $modname = xarMod::getName();
            }
        }
        $modid = xarMod::getRegId($modname);
        if (empty($modid)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'module name',
                'admin',
                'deletehook',
                'xarcachemanager'
            );
            throw new Exception($msg);
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

        switch ($modname) {
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
                sys::import('modules.dynamicdata.class.objects.descriptor');
                $objectinfo = DataObjectDescriptor::getObjectID(['moduleid'  => $modid,
                                                                 'itemtype' => $itemtype, ]);
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
                    $hooklist = xarMod::apiFunc('modules', 'admin', 'gethooklist');
                    $modhooks = reset($hooklist[$modname]);

                    foreach ($modhooks as $hookedmodname => $hookedmod) {
                        $cacheKey = "$hookedmodname-";
                        xarPageCache::flushCached($cacheKey);
                    }
                }
                // incase it's got a user view, like categories.
                // fall-through
                // no break
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

        if (xarCache::$outputCacheIsEnabled && xarModVars::get('xarcachemanager', 'AutoRegenSessionless')) {
            self::regenstatic();
        }

        // Return the extra info
        return $extrainfo;
    }

    /**
     * update entry for a module item - hook for ('item','updateconfig','API')
     * Optional $extrainfo['xarcachemanager_remark'] from arguments, or 'xarcachemanager_remark' from input
     *
     * @param array $args with mandatory arguments:
     * - array $args['extrainfo'] extra information
     * @return array updated extrainfo array
     * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
     * @todo - actually raise errors, get intelligent and specific about cache files to remove
     */
    public static function updateconfighook($args)
    {
        extract($args);

        if (!isset($extrainfo) || !is_array($extrainfo)) {
            $extrainfo = [];
        }

        if (!xarCache::$outputCacheIsEnabled) {
            // nothing more to do here
            return $extrainfo;
        }

        // When called via hooks, modname wil be empty, but we get it from the
        // extrainfo or the current module
        if (empty($modname)) {
            if (!empty($extrainfo['module'])) {
                $modname = $extrainfo['module'];
            } else {
                $modname = xarMod::getName();
            }
        }
        $modid = xarMod::getRegId($modname);
        if (empty($modid)) {
            $msg = xarMLS::translate(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'module name',
                'admin',
                'updatehook',
                'xarcachemanager'
            );
            throw new Exception($msg);
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

        switch ($modname) {
            case 'base': // who knows what global impact a config change to base might make
                // flush everything.
                if (xarOutputCache::$pageCacheIsEnabled) {
                    xarPageCache::flushCached('');
                }
                if (xarOutputCache::$blockCacheIsEnabled) {
                    xarBlockCache::flushCached('');
                }
                break;
            case 'autolinks': // fall-through all hooked utility modules that are admin config modified
            case 'comments': // keep falling through
            case 'keywords': // keep falling through
                // delete cachekey of each module autolinks is hooked to.
                if (xarOutputCache::$pageCacheIsEnabled) {
                    $hooklist = xarMod::apiFunc('modules', 'admin', 'gethooklist');
                    $modhooks = reset($hooklist[$modname]);

                    foreach ($modhooks as $hookedmodname => $hookedmod) {
                        $cacheKey = "$hookedmodname-";
                        xarPageCache::flushCached($cacheKey);
                    }
                }
                // incase it's got a user view, like categories.
                // no break
            case 'articles': // fall-through
                //nothing special yet
            default:
                // identify pages that include the updated item and delete the cached files
                // nothing fancy yet, just flush it out
                $cacheKey = "$modname-";
                if (xarOutputCache::$pageCacheIsEnabled) {
                    xarPageCache::flushCached($cacheKey);
                }
                // since we're modifying the config, we might get a new admin menulink
                if (xarOutputCache::$blockCacheIsEnabled) {
                    xarBlockCache::flushCached('base-block');
                }
                break;
        }

        if (xarModVars::get('xarcachemanager', 'AutoRegenSessionless')) {
            self::regenstatic();
        }

        // Return the extra info
        return $extrainfo;
    }
}
