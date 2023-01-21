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
sys::import('modules.xarcachemanager.class.config');
use Xaraya\Modules\CacheManager\CacheConfig;

/**
 * @author jsb
 * @uses CacheConfig::getTypes()
 * @return array Cache types, with key set to cache type and value set to its settings
 */
function xarcachemanager_adminapi_getcachetypes()
{
    // return the cache types and their settings
    return CacheConfig::getTypes();
}
