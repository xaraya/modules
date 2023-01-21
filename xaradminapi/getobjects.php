<?php
/**
 * Get configuration of object caching for objects
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.config.objectcache');
use Xaraya\Modules\CacheManager\Config\ObjectCache;

/**
 * get configuration of object caching for all objects
 * @uses ObjectCache::getConfig()
 * @return array object caching configurations
 */
function xarcachemanager_adminapi_getobjects($args)
{
    extract($args);

    // Get all object cache settings
    return ObjectCache::getConfig();
}
