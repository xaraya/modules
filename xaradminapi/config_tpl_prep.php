<?php
/**
 * Save configuration settings
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.manager');
use Xaraya\Modules\CacheManager\CacheManager;

/**
 * Save configuration settings in the config file and modVars
 *
 * @author jsb <jsb@xaraya.com>
 * @access public
 * @uses CacheManager::config_tpl_prep()
 * @param array $cachingConfiguration cachingConfiguration to be prep for a template
 * @return array of cachingConfiguration with '.' removed from keys or void
 */
function xarcachemanager_adminapi_config_tpl_prep($cachingConfiguration)
{
    return CacheManager::config_tpl_prep($cachingConfiguration);
}
