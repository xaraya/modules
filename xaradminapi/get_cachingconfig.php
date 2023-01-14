<?php
/**
 * Get caching config settings
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
 * Gets caching configuration settings in the config file or modVars
 *
 * @author jsb <jsb@xaraya.com>
 * @access public
 * @uses CacheManager::get_config()
 * @param string $args['from'] source of configuration to get - file or db
 * @param array $args['keys'] array of config labels and values
 * @param boolean $args['tpl_prep'] prep the config for use in templates
 * @param boolean $args['viahook'] config value requested as part of a hook call
 * @return array of caching configuration settings
 * @throws MODULE_FILE_NOT_EXIST
 */
function xarcachemanager_adminapi_get_cachingconfig($args)
{
    return CacheManager::get_config($args);
}
