<?php

/**
 * get configuration of block caching for all blocks
 *
 * @returns array
 * @return array of block caching configurations
 */
function xarcachemanager_adminapi_getblocks($args)
{
    extract($args);

    $systemPrefix = xarDBGetSystemTablePrefix();
    $blocksettings = $systemPrefix . '_cache_blocks';
    $dbconn =& xarDBGetConn();
    $query = "SELECT xar_bid,
             xar_nocache,
             xar_page,
             xar_user,
             xar_expire
             FROM $blocksettings";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get all block instances
    $blocks = xarModAPIfunc('blocks', 'user', 'getall');
    $bid2key = array();
    foreach ($blocks as $key => $block) {
        $bid2key[$block['bid']] = $key;
    }

    while (!$result->EOF) {
        list ($bid, $nocache, $pageshared, $usershared, $cacheexpire) = $result->fields;
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
        if (empty($cacheexpire)) {
            $cacheexpire = 0;
        }
        if ($cacheexpire > 0 ) {
            $cacheexpire = xarModAPIFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                          array('starttime' => $cacheexpire,
                                                'direction' => 'from'));
        } else {
            $cacheexpire = '';
        }
        $key = $bid2key[$bid];
        $blocks[$key]['nocache'] = $nocache;
        $blocks[$key]['pageshared'] = $pageshared;
        $blocks[$key]['usershared'] = $usershared;
        $blocks[$key]['cacheexpire'] = $cacheexpire;
    }
    foreach ($blocks as $key => $block) {
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

?>
