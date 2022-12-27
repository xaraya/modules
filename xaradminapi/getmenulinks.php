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
    $menulinks = [];

    // Security Check
    if (!xarSecurity::check('AdminXarCache')) {
        return $menulinks;
    }

    $menulinks[] = ['url'   => xarController::URL(
        'xarcachemanager',
        'admin',
        'flushcache'
    ),
                         'title' => xarMLS::translate('Flush the output cache of xarCache'),
                         'label' => xarMLS::translate('Flush Cache'), ];

    if (xarCache::$outputCacheIsEnabled) {
        if (xarOutputCache::$pageCacheIsEnabled) {
            $menulinks[] = ['url'   => xarController::URL(
                'xarcachemanager',
                'admin',
                'pages'
            ),
                                 'title' => xarMLS::translate('Configure the caching options for pages'),
                                 'label' => xarMLS::translate('Page Caching'), ];
        }
        if (xarOutputCache::$blockCacheIsEnabled) {
            $menulinks[] = ['url'   => xarController::URL(
                'xarcachemanager',
                'admin',
                'blocks'
            ),
                                 'title' => xarMLS::translate('Configure the caching options for each block'),
                                 'label' => xarMLS::translate('Block Caching'), ];
        }
        if (xarOutputCache::$moduleCacheIsEnabled) {
            $menulinks[] = ['url'   => xarController::URL(
                'xarcachemanager',
                'admin',
                'modules'
            ),
                                 'title' => xarMLS::translate('Configure the caching options for modules'),
                                 'label' => xarMLS::translate('Module Caching'), ];
        }
        if (xarOutputCache::$objectCacheIsEnabled) {
            $menulinks[] = ['url'   => xarController::URL(
                'xarcachemanager',
                'admin',
                'objects'
            ),
                                 'title' => xarMLS::translate('Configure the caching options for objects'),
                                 'label' => xarMLS::translate('Object Caching'), ];
        }
    }
    /*
        if (xarCache::$queryCacheIsEnabled) {
            $menulinks[] = Array('url'   => xarController::URL('xarcachemanager',
                                                      'admin',
                                                      'queries'),
                                 'title' => xarMLS::translate('Configure the caching options for queries'),
                                 'label' => xarMLS::translate('Query Caching'));
        }
        if (xarCache::$variableCacheIsEnabled) {
            $menulinks[] = Array('url'   => xarController::URL('xarcachemanager',
                                                      'admin',
                                                      'variables'),
                                 'title' => xarMLS::translate('Configure the caching options for variables'),
                                 'label' => xarMLS::translate('Variable Caching'));
        }
    */
    $menulinks[] = ['url'   => xarController::URL(
        'xarcachemanager',
        'admin',
        'stats'
    ),
                         'title' => xarMLS::translate('View cache statistics'),
                         'label' => xarMLS::translate('View Statistics'), ];
    $menulinks[] = ['url'   => xarController::URL(
        'xarcachemanager',
        'admin',
        'modifyconfig'
    ),
                         'title' => xarMLS::translate('Modify the xarCache configuration'),
                         'label' => xarMLS::translate('Modify Config'), ];

    return $menulinks;
}
