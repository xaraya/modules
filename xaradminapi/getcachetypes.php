<?php
/**
 * Construct output array
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
 * @author jsb
 *
 * @return array Cache types, with key set to cache type and value set to its settings
 */
function xarcachemanager_adminapi_getcachetypes()
{
    static $cachetypes;
    if (!empty($cachetypes)) return $cachetypes;

    // list of currently supported cache types
    $typelist = array('page', 'mod', 'block');

    // get the caching config settings from the config file
    $settings = xarModAPIFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                              array('from' => 'file'));

    // map the settings to the right cache type
    $cachetypes = array();
    foreach ($typelist as $type) {
        $cachetypes[$type] = array();
        foreach (array_keys($settings) as $setting) {
            if (preg_match("/^$type\.(.+)$/i",$setting,$matches)) {
                $info = $matches[1];
                $cachetypes[$type][$info] = $settings[$setting];
            }
        }
        // default cache storage is 'filesystem' if necessary
        if (empty($cachetypes[$type]['CacheStorage'])) {
            $cachetypes[$type]['CacheStorage'] = 'filesystem';
        }
    }

    // return the cache types and their settings
    return $cachetypes;
}
?>
