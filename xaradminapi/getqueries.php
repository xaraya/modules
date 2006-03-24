<?php
/*
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
/**
 * get configuration of query caching for expensive queries
 *
 * @returns array
 * @return array of query caching configurations
 */
function xarcachemanager_adminapi_getqueries($args)
{
    extract($args);

    $queries = array();

// TODO: add some configuration options for query caching in the core
    $queries['core'] = array('TODO' => 0);

// TODO: enable $dbconn->LogSQL() and check expensive SQL queries for new candidates

    $candidates = array(
                        'articles' => array('userapi.getall'), // TODO: round off current pubdate
                        'categories' => array('userapi.getcat'),
                        'comments' => array('userapi.get_author_count',
                                            'userapi.get_multiple'),
                        'dynamicdata' => array(), // TODO: make dependent on arguments
                        'privileges' => array(),
                        'roles' => array('userapi.countall',
                                         'userapi.getall',
                                         'userapi.countallactive',
                                         'userapi.getallactive'),
                        'xarbb' => array('userapi.countposts',
                                         'userapi.getalltopics'),
                       );

    foreach ($candidates as $module => $querylist) {
        if (!xarModIsAvailable($module)) continue;
        $queries[$module] = array();
        foreach ($querylist as $query) {
// stored in module variables (for now ?)
            $queries[$module][$query] = xarModGetVar($module,'cache.'.$query);
        }
    }

    return $queries;
}

?>
