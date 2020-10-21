<?php
/**
 * Save config
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.cache_manager');

/**
 * Save configuration settings in the config file and modVars
 *
 * @author jsb <jsb@xaraya.com>
 * @access public
 * @param $args['config'] array of config labels and values
 * @throws FUNCTION_FAILED
 */
function xarcachemanager_adminapi_save_cachingconfig($args)
{
    return xarCache_Manager::save_config($args);
}
