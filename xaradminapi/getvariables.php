<?php
/**
 * Get configuration of variable caching for variables
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.config.variablecache');
use Xaraya\Modules\CacheManager\Config\VariableCache;

/**
 * get configuration of variable caching for all variables
 * @uses VariableCache::getConfig()
 * @return array variable caching configurations
 */
function xarcachemanager_adminapi_getvariables($args)
{
    extract($args);

    // Get all variable cache settings
    return VariableCache::getConfig();
}
