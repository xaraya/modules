<?php
/**
 * Construct an array of current cache items
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
 * Construct an array of the current cache items
 *
 * @author jsb
 * @uses CacheInfo::getList()
 * @param array $args['type'] cachetype to get the cache items from
 * @return array array of cache items
*/
function xarcachemanager_adminapi_getcachelist($args = ['type' => ''])
{
    $type = '';
    if (is_array($args)) {
        extract($args);
    } else {
        $type = $args;
    }
    return CacheInfo::getList($type);
}
