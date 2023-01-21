<?php
/**
 * Config module caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.config.modulecache');
use Xaraya\Modules\CacheManager\Config\ModuleCache;

/**
 * configure module caching
 * @uses ModuleCache::modifyConfig()
 * @return array
 */
function xarcachemanager_admin_modules($args)
{
    return ModuleCache::modifyConfig($args);
}
