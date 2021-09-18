<?php
/**
 * Get queries caching config
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.cache_manager');

/**
 * get configuration of query caching for expensive queries
 *
 * @return array of query caching configurations
 */
function xarcachemanager_adminapi_getqueries($args)
{
    extract($args);

    $queries = [];

    // TODO: add some configuration options for query caching in the core
    $queries['core'] = ['TODO' => 0];

    // TODO: enable $dbconn->LogSQL() and check expensive SQL queries for new candidates

    $candidates = [
                        'articles' => ['userapi.getall'], // TODO: round off current pubdate
                        'categories' => ['userapi.getcat'],
                        'comments' => ['userapi.get_author_count',
                                            'userapi.get_multiple', ],
                        'dynamicdata' => [], // TODO: make dependent on arguments
                        'privileges' => [],
                        'roles' => ['userapi.countall',
                                         'userapi.getall',
                                         'userapi.countallactive',
                                         'userapi.getallactive', ],
                        'xarbb' => ['userapi.countposts',
                                         'userapi.getalltopics', ],
                       ];

    foreach ($candidates as $module => $querylist) {
        if (!xarMod::isAvailable($module)) {
            continue;
        }
        $queries[$module] = [];
        foreach ($querylist as $query) {
            // stored in module variables (for now ?)
            $queries[$module][$query] = xarModVars::get($module, 'cache.'.$query);
        }
    }

    return $queries;
}
