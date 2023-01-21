<?php
/**
 * Get cache size
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
 * @author jsb
 * @uses CacheInfo::getSize()
 * @param array $args['type'] cachetype to get the size for
 * @return int size of the cache
*/
function xarcachemanager_adminapi_getcachesize($args = ['type' => ''])
{
    $type = '';
    if (is_array($args)) {
        extract($args);
    } else {
        $type = $args;
    }
    return CacheInfo::getSize($type);
}
