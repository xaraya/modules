<?php
/**
 * Utility function for menulinks
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
 * utility function pass individual menu items to the main menu
 *
 * @author jsb| mikespub
 * @return array containing the menulinks for the main menu items.
 */
function xarcachemanager_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminXarCache')) {
        $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                  'admin',
                                                  'queries'),
                             'title' => xarML('Configure the caching options for queries'),
                             'label' => xarML('Query Caching'));
        $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                  'admin',
                                                  'flushcache'),
                             'title' => xarML('Flush the output cache of xarCache'),
                             'label' => xarML('Flush Cache'));
        $varCacheDir = xarCoreGetVarDirPath() . '/cache';
        if (file_exists($varCacheDir . '/output/cache.pagelevel')) {
            $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                      'admin',
                                                      'pages'),
                                 'title' => xarML('Configure the caching options for pages'),
                                 'label' => xarML('Page Caching'));
        }
        if (file_exists($varCacheDir . '/output/cache.blocklevel')) {
            $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                      'admin',
                                                      'blocks'),
                                 'title' => xarML('Configure the caching options for each block'),
                                 'label' => xarML('Block Caching'));
        }
        $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                  'admin',
                                                  'stats'),
                             'title' => xarML('View cache statistics'),
                             'label' => xarML('View Statistics'));
        $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Modify the xarCache configuration'),
                             'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
