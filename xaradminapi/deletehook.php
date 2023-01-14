<?php
/**
 * Delete entry for a module item
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.hooks');

/**
 * delete entry for a module item - hook for ('item','delete','API')
 *
 * @uses xarCache_Hooks::deletehook()
 * @param array $args with mandatory arguments:
 * - int   $args['objectid'] ID of the object
 * - array $args['extrainfo'] extra information
 * @return array updated extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @todo - actually raise errors, get intelligent and specific about cache files to remove
 */
function xarcachemanager_adminapi_deletehook($args)
{
    return xarCache_Hooks::deletehook($args);
}
