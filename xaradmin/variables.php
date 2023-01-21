<?php
/**
 * Config variable caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.config.variablecache');
use Xaraya\Modules\CacheManager\Config\VariableCache;

/**
 * configure variable caching
 * @uses VariableCache::modifyConfig()
 * @return array
 */
function xarcachemanager_admin_variables($args)
{
    return VariableCache::modifyConfig($args);
}
