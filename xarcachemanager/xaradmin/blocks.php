<?php

/**
 * configure block caching
 */
function xarcachemanager_admin_blocks($args)
{ 
    extract($args);

    if (!xarSecurityCheck('AdminXarCache')) return;

    $data = array();
    $iscached = xarModGetVar('xarcachemanager','CacheBlockOutput');
    if (empty($iscached)) {
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
            } else {
                $expire = 0;
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
                        VALUES ('" . xarVarPrepForStore($block['bid']) . "',
                                '" . xarVarPrepForStore($block['nocache']) . "',
                                '" . xarVarPrepForStore($block['pageshared']) . "',
                                '" . xarVarPrepForStore($block['usershared']) . "',
                                '" . xarVarPrepForStore($block['cacheexpire']) . "')";
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        }

        // blocks could be anywhere, we're not smart enough not know exactly where yet
        // so just flush all pages
        $cacheKey = "-user-";
        xarPageFlushCached($cacheKey);
        // and flush the blocks
        $cacheKey = "-blockid";
        xarPageFlushCached($cacheKey);
    }

    // Get all block caching configurations
    $data['blocks'] = xarModAPIfunc('xarcachemanager', 'admin', 'getblocks');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
