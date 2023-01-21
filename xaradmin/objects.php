<?php
/**
 * Config object caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.config.objectcache');
use Xaraya\Modules\CacheManager\Config\ObjectCache;

/**
 * configure object caching
 * @uses ObjectCache::modifyConfig()
 * @return array
 */
function xarcachemanager_admin_objects($args)
{
    return ObjectCache::modifyConfig($args);
}
