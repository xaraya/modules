<?php
/**
 * Construct an array of current cache keys
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
 * Construct an array of the current cache keys
 *
 * @author jsb
 * @uses CacheInfo::getKeys()
 * @param array $args['type'] cachetype to get the cache keys from
 * @return array sorted array of cachekeys
*/
function xarcachemanager_adminapi_getcachekeys($args = ['type' => ''])
{
    $type = '';
    if (is_array($args)) {
        extract($args);
    } else {
        $type = $args;
    }
    return CacheInfo::getKeys($type);
}
