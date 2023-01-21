<?php
/**
 * Construct an array of current cache info
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.info');
use Xaraya\Modules\CacheManager\CacheInfo;

/**
 * Construct an array of the current cache item
 *
 * @author jsb
 * @uses CacheInfo::getItem()
 * @param array $args['type'] cachetype to get the cache item from, with $args['key'] the cache key
 * @return array array of cacheitem
*/
function xarcachemanager_adminapi_getcacheitem($args = ['type' => '', 'key' => '', 'code' => ''])
{
    $type = '';
    $key = '';
    $code = '';
    if (is_array($args)) {
        extract($args);
    } else {
        $type = $args;
    }
    return CacheInfo::getItem($type, $key, $code);
}
