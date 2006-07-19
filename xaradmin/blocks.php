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

    $cacheOutputDir = xarCoreGetVarDirPath() . '/cache/output';

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
                $expire = xarModAPIFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                          array('starttime' => $expire,
                                                'direction' => 'to'));
            } elseif ($expire === '0') {
                $expire = 0;
            } else {
                $expire = NULL;
            }
            $newblocks[$bid]['cacheexpire'] = $expire;
        }
        $systemPrefix = xarDBGetSystemTablePrefix();
        $blocksettings = $systemPrefix . '_cache_blocks';
        $dbconn =& xarDBGetConn();

        // delete the whole cache blocks table and insert the new values
        $query = "DELETE FROM $blocksettings";
        $result =& $dbconn->Execute($query);
        if (!$result) return;

        foreach ($newblocks as $block) {
            $query = "INSERT INTO $blocksettings (xar_bid,
                                                  xar_nocache,
                                                  xar_page,
                                                  xar_user,
                                                  xar_expire)
                        VALUES (?,?,?,?,?)";
            $bindvars = array($block['bid'], $block['nocache'], $block['pageshared'], $block['usershared'], $block['cacheexpire']);
            $result =& $dbconn->Execute($query,$bindvars);
            if (!$result) return;
        }

        // make sure we can flush blocks, even if caching is currently disabled
        if (!function_exists('xarOutputFlushCached')) {
            include_once 'includes/xarCache.php';
            if (!xarCache_init(array('cacheDir' => $cacheOutputDir))) {
                // somethings wrong, caching should be disabled now
                return;
            }
        }
        // blocks could be anywhere, we're not smart enough not know exactly where yet
        // so just flush all pages
        xarOutputFlushCached('', $cacheOutputDir . '/page');
        // and flush the blocks
        xarOutputFlushCached('', $cacheOutputDir . '/block');
        if (xarModGetVar('xarcachemanager','AutoRegenSessionless')) {
            xarModAPIFunc( 'xarcachemanager', 'admin', 'regenstatic');
        }
    }

    // Get all block caching configurations
    $data['blocks'] = xarModAPIfunc('xarcachemanager', 'admin', 'getblocks');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
