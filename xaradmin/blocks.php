<?php
/**
 * Config block caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.cache_manager');

/**
 * configure block caching
 * @return array
 */
function xarcachemanager_admin_blocks($args)
{
    extract($args);

    if (!xarSecurity::check('AdminXarCache')) {
        return;
    }

    $data = array();
    if (!xarCache::$outputCacheIsEnabled || !xarOutputCache::$blockCacheIsEnabled) {
        $data['blocks'] = array();
        return $data;
    }

    xarVar::fetch('submit', 'str', $submit, '');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        xarVar::fetch('docache', 'isset', $docache, array());
        xarVar::fetch('pageshared', 'isset', $pageshared, array());
        xarVar::fetch('usershared', 'isset', $usershared, array());
        xarVar::fetch('cacheexpire', 'isset', $cacheexpire, array());

        $newblocks = array();
        // loop over something that should return values for every block
        foreach ($cacheexpire as $bid => $expire) {
            $newblocks[$bid] = array();
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
                $expire = xarCache_Manager::convertseconds(
                    array('starttime' => $expire,
                                                'direction' => 'to')
                );
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
        $result =& $dbconn->Execute($query);
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
            $bindvars = array($block['bid'], $block['nocache'], $block['pageshared'], $block['usershared'], $block['cacheexpire']);
            $result =& $dbconn->Execute($query, $bindvars);
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
    $data['blocks'] = xarMod::apiFunc('xarcachemanager', 'admin', 'getblocks');

    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
