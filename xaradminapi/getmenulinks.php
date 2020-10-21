<?php
/**
 * Utility function for menulinks
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
 * utility function pass individual menu items to the main menu
 *
 * @author jsb| mikespub
 * @return array containing the menulinks for the main menu items.
 */
function xarcachemanager_adminapi_getmenulinks()
{
    $menulinks = array();

    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) {
        return $menulinks;
    }

    $menulinks[] = array('url'   => xarModURL(
        'xarcachemanager',
        'admin',
        'flushcache'
    ),
                         'title' => xarML('Flush the output cache of xarCache'),
                         'label' => xarML('Flush Cache'));

    if (xarCache::$outputCacheIsEnabled) {
        if (xarOutputCache::$pageCacheIsEnabled) {
            $menulinks[] = array('url'   => xarModURL(
                'xarcachemanager',
                'admin',
                'pages'
            ),
                                 'title' => xarML('Configure the caching options for pages'),
                                 'label' => xarML('Page Caching'));
        }
        if (xarOutputCache::$blockCacheIsEnabled) {
            $menulinks[] = array('url'   => xarModURL(
                'xarcachemanager',
                'admin',
                'blocks'
            ),
                                 'title' => xarML('Configure the caching options for each block'),
                                 'label' => xarML('Block Caching'));
        }
        if (xarOutputCache::$moduleCacheIsEnabled) {
            $menulinks[] = array('url'   => xarModURL(
                'xarcachemanager',
                'admin',
                'modules'
            ),
                                 'title' => xarML('Configure the caching options for modules'),
                                 'label' => xarML('Module Caching'));
        }
        if (xarOutputCache::$objectCacheIsEnabled) {
            $menulinks[] = array('url'   => xarModURL(
                'xarcachemanager',
                'admin',
                'objects'
            ),
                                 'title' => xarML('Configure the caching options for objects'),
                                 'label' => xarML('Object Caching'));
        }
    }
    /*
        if (xarCache::$queryCacheIsEnabled) {
            $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                      'admin',
                                                      'queries'),
                                 'title' => xarML('Configure the caching options for queries'),
                                 'label' => xarML('Query Caching'));
        }
        if (xarCache::$variableCacheIsEnabled) {
            $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                      'admin',
                                                      'variables'),
                                 'title' => xarML('Configure the caching options for variables'),
                                 'label' => xarML('Variable Caching'));
        }
    */
    $menulinks[] = array('url'   => xarModURL(
        'xarcachemanager',
        'admin',
        'stats'
    ),
                         'title' => xarML('View cache statistics'),
                         'label' => xarML('View Statistics'));
    $menulinks[] = array('url'   => xarModURL(
        'xarcachemanager',
        'admin',
        'modifyconfig'
    ),
                         'title' => xarML('Modify the xarCache configuration'),
                         'label' => xarML('Modify Config'));

    return $menulinks;
}
