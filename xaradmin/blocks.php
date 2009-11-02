<?php
/**
 * Config block caching
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
 * configure block caching
 */
function xarcachemanager_admin_blocks($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminXarCache')) { return; }

    $cacheOutputDir = sys::varpath() . '/cache/output';

    $data = array();
    if (!file_exists($cacheOutputDir . '/cache.blocklevel')) {
        $data['blocks'] = array();
        return $data;
    }

    xarVarFetch('submit','str',$submit,'');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        xarVarFetch('nocache','isset',$nocache,array());
        xarVarFetch('pageshared','isset',$pageshared,array());
        xarVarFetch('usershared','isset',$usershared,array());
        xarVarFetch('cacheexpire','isset',$cacheexpire,array());

        $newblocks = array();
        // loop over something that should return values for every block
        foreach ($cacheexpire as $bid => $expire) {
            $newblocks[$bid] = array();
            $newblocks[$bid]['bid'] = $bid;
            if (!empty($nocache[$bid])) {
                $newblocks[$bid]['nocache'] = 1;
            } else {
                $newblocks[$bid]['nocache'] = 0;
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
                $expire = xarMod::apiFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                          array('starttime' => $expire,
                                                'direction' => 'to'));
            } elseif ($expire === '0') {
                $expire = 0;
            } else {
                $expire = NULL;
            }
            $newblocks[$bid]['cacheexpire'] = $expire;
        }
        $systemPrefix = xarDB::getPrefix();
        $blocksettings = $systemPrefix . '_cache_blocks';
        $dbconn = xarDB::getConn();

        // delete the whole cache blocks table and insert the new values
        $query = "DELETE FROM $blocksettings";
        $result =& $dbconn->Execute($query);
        if (!$result) return;

        foreach ($newblocks as $block) {
            $query = "INSERT INTO $blocksettings (blockinstance_id,
                                                  nocache,
                                                  page,
                                                  theuser,
                                                  expire)
                        VALUES (?,?,?,?,?)";
            $bindvars = array($block['bid'], $block['nocache'], $block['pageshared'], $block['usershared'], $block['cacheexpire']);
            $result =& $dbconn->Execute($query,$bindvars);
            if (!$result) return;
        }

        // make sure we can flush blocks, even if caching is currently disabled
        if (!xarCache::$outputCacheIsEnabled) {
            sys::import('xaraya.caching.output');
            //xarCache::$outputCacheIsEnabled = xarOutputCache::init();
            xarOutputCache::init();
        }

        // get the caching config settings from the config file
        $config = xarMod::apiFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                                  array('from' => 'file'));

        // blocks could be anywhere, we're not smart enough not know exactly where yet
        $key = '';
        // so just flush all pages
        if (!xarOutputCache::$pageCacheIsEnabled) {
            sys::import('xaraya.caching.output.page');
            xarPacheCache::init($config);
        }
        xarPageCache::flushCached($key);
        // and flush the blocks
        if (!xarOutputCache::$blockCacheIsEnabled) {
            sys::import('xaraya.caching.output.block');
            xarBlockCache::init($config);
        }
        xarBlockCache::flushCached($key);
        if (xarModVars::get('xarcachemanager','AutoRegenSessionless')) {
            xarMod::apiFunc( 'xarcachemanager', 'admin', 'regenstatic');
        }
    }

    // Get all block caching configurations
    $data['blocks'] = xarModAPIfunc('xarcachemanager', 'admin', 'getblocks');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
