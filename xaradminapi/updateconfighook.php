<?php
/**
 * Update entry for an item
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.cache_hooks');

/**
 * update entry for a module item - hook for ('item','updateconfig','API')
 * Optional $extrainfo['xarcachemanager_remark'] from arguments, or 'xarcachemanager_remark' from input
 *
 * @param array $args with mandatory arguments:
 * - array $args['extrainfo'] extra information
 * @return array updated extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @todo - actually raise errors, get intelligent and specific about cache files to remove
 */
function xarcachemanager_adminapi_updateconfighook($args)
{
    return xarCache_Hooks::updateconfighook($args);
}
