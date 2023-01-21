<?php
/**
 * Get configuration of module caching for modules
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.config.modulecache');
use Xaraya\Modules\CacheManager\Config\ModuleCache;

/**
 * get configuration of module caching for all modules
 * @uses ModuleCache::getConfig()
 * @return array module caching configurations
 */
function xarcachemanager_adminapi_getmodules($args)
{
    extract($args);

    // Get all module cache settings
    return ModuleCache::getConfig();
}
