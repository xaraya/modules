<?php
/**
 * Restore caching config
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

/**
 * Restore the caching configuration file
 *
 * @author jsb <jsb@xaraya.com>
 * @access public
 * @uses xarCache_Manager::restore_config()
 * @throws FUNCTION_FAILED
 * @return boolean
 */
function xarcachemanager_adminapi_restore_cachingconfig()
{
    return xarCache_Manager::restore_config();
}
