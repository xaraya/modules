<?php
/**
 * Classes to manage config for the cache system of Xaraya
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

namespace Xaraya\Modules\CacheManager\Config;

use xarSecurity;
use xarCache;
use xarOutputCache;
use xarPageCache;
use xarVar;
use xarSec;
use xarModVars;
use xarMod;
use xarDB;
use xarBlockCache;
use sys;

sys::import('modules.xarcachemanager.class.config');
sys::import('modules.xarcachemanager.class.utility');
use Xaraya\Modules\CacheManager\CacheConfig;
use Xaraya\Modules\CacheManager\CacheUtility;

class BlockCache extends CacheConfig
{
    public static function init(array $args = [])
    {
    }

    /**
     * configure block caching
     * @return array
     */
    public static function modifyConfig($args)
    {
        extract($args);

        if (!xarSecurity::check('AdminXarCache')) {
            return;
        }

        $data = [];
        if (!xarCache::$outputCacheIsEnabled || !xarOutputCache::$blockCacheIsEnabled) {
            $data['blocks'] = [];
            return $data;
        }

        xarVar::fetch('submit', 'str', $submit, '');
        if (!empty($submit)) {
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) {
                return;
            }

            xarVar::fetch('docache', 'isset', $docache, []);
            xarVar::fetch('pageshared', 'isset', $pageshared, []);
            xarVar::fetch('usershared', 'isset', $usershared, []);
            xarVar::fetch('cacheexpire', 'isset', $cacheexpire, []);

            $newblocks = [];
            // loop over something that should return values for every block
            foreach ($cacheexpire as $bid => $expire) {
                $newblocks[$bid] = [];
                $newblocks[$bid]['bid'] = $bid;
                // flip from docache in template to nocache in settings
                if (!empty($docache[$bid])) {
                    $newblocks[$bid]['nocache'] = 0;
                } else {
                    $newblocks[$bid]['nocache'] = 1;
                }
                if (!empty($pageshared[$bid])) {
                    $newblocks[$bid]['pageshared'] = 1;
                } else {
                    $newblocks[$bid]['pageshared'] = 0;
                }
                if (!empty($usershared[$bid])) {
                    $newblocks[$bid]['usershared'] = intval($usershared[$bid]);
                } else {
                    $newblocks[$bid]['usershared'] = 0;
                }
                if (!empty($expire)) {
                    $expire = CacheUtility::convertToSeconds($expire);
                } elseif ($expire === '0') {
                    $expire = 0;
                } else {
                    $expire = null;
                }
                $newblocks[$bid]['cacheexpire'] = $expire;
            }
            $systemPrefix = xarDB::getPrefix();
            $blocksettings = $systemPrefix . '_cache_blocks';
            $dbconn = xarDB::getConn();

            // delete the whole cache blocks table and insert the new values
            $query = "DELETE FROM $blocksettings";
            $result = $dbconn->Execute($query);
            if (!$result) {
                return;
            }

            foreach ($newblocks as $block) {
                $query = "INSERT INTO $blocksettings (blockinstance_id,
                                                    nocache,
                                                    page,
                                                    theuser,
                                                    expire)
                            VALUES (?,?,?,?,?)";
                $bindvars = [$block['bid'], $block['nocache'], $block['pageshared'], $block['usershared'], $block['cacheexpire']];
                $result = $dbconn->Execute($query, $bindvars);
                if (!$result) {
                    return;
                }
            }

            // blocks could be anywhere, we're not smart enough not know exactly where yet
            $key = '';
            // so just flush all pages
            if (xarOutputCache::$pageCacheIsEnabled) {
                xarPageCache::flushCached($key);
            }
            // and flush the blocks
            xarBlockCache::flushCached($key);
            if (xarModVars::get('xarcachemanager', 'AutoRegenSessionless')) {
                xarMod::apiFunc('xarcachemanager', 'admin', 'regenstatic');
            }
        }

        // Get all block caching configurations
        $data['blocks'] = static::getConfig();

        $data['authid'] = xarSec::genAuthKey();
        return $data;
    }

    /**
     * get configuration of block caching for all blocks
     *
     * @return array Block caching configurations
     */
    public static function getConfig()
    {
        $systemPrefix = xarDB::getPrefix();
        $blocksettings = $systemPrefix . '_cache_blocks';
        $dbconn = xarDB::getConn();
        $query = "SELECT blockinstance_id,
                nocache,
                page,
                theuser,
                expire
                FROM $blocksettings";
        $result = $dbconn->Execute($query);
        if (!$result) {
            return;
        }

        // Get all block instances
        $blocks = xarMod::apiFunc('blocks', 'user', 'getall');
        $bid2key = [];
        foreach ($blocks as $key => $block) {
            $bid2key[$block['bid']] = $key;
        }

        while (!$result->EOF) {
            [$bid, $nocache, $pageshared, $usershared, $cacheexpire] = $result->fields;
            $result->MoveNext();
            if (!isset($bid2key[$bid])) {
                continue;
            }
            if (empty($nocache)) {
                $nocache = 0;
            }
            if (empty($pageshared)) {
                $pageshared = 0;
            }
            if (empty($usershared)) {
                $usershared = 0;
            }
            /*if (empty($cacheexpire)) {
                $cacheexpire = 0;
            }*/
            if ($cacheexpire > 0) {
                $cacheexpire = CacheUtility::convertFromSeconds($cacheexpire);
            }

            $key = $bid2key[$bid];
            $blocks[$key]['nocache'] = $nocache;
            $blocks[$key]['pageshared'] = $pageshared;
            $blocks[$key]['usershared'] = $usershared;
            $blocks[$key]['cacheexpire'] = $cacheexpire;
        }
        foreach ($blocks as $key => $block) {
            if (!isset($block['nocache'])) {
                // Try loading some defaults from the block init array (cfr. articles/random)
                if (!empty($block['module']) && !empty($block['type'])) {
                    $initresult = xarMod::apiFunc(
                        'blocks',
                        'user',
                        'read_type_init',
                        ['module' => $block['module'],
                        'type' => $block['type'], ]
                    );
                    if (!empty($initresult) && is_array($initresult)) {
                        if (isset($initresult['nocache'])) {
                            $block['nocache'] = $initresult['nocache'];
                            $blocks[$key]['nocache'] = $initresult['nocache'];
                        }
                        if (isset($initresult['pageshared'])) {
                            $block['pageshared'] = $initresult['pageshared'];
                            $blocks[$key]['pageshared'] = $initresult['pageshared'];
                        }
                        if (isset($initresult['usershared'])) {
                            $block['usershared'] = $initresult['usershared'];
                            $blocks[$key]['usershared'] = $initresult['usershared'];
                        }
                        if (isset($initresult['cacheexpire'])) {
                            $block['cacheexpire'] = $initresult['cacheexpire'];
                            $blocks[$key]['cacheexpire'] = $initresult['cacheexpire'];
                        }
                    }
                }
            }
            if (!isset($block['nocache'])) {
                $blocks[$key]['nocache'] = 0;
            }
            if (!isset($block['pageshared'])) {
                $blocks[$key]['pageshared'] = 0;
            }
            if (!isset($block['usershared'])) {
                $blocks[$key]['usershared'] = 0;
            }
            if (!isset($block['cacheexpire'])) {
                $blocks[$key]['cacheexpire'] = '';
            }
        }
        return $blocks;
    }
}
