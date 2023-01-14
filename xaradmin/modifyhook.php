<?php
/**
 * Modify hook
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
 * modify an entry for a module item - hook for ('item','modify','GUI')
 *
 * @uses xarCache_Hooks::modifyhook()
 * @param array $args with mandatory arguments:
 * - int   $args['objectid'] ID of the object
 * - array $args['extrainfo'] extra information
 * @return string hook output in HTML
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarcachemanager_admin_modifyhook($args)
{
    return xarCache_Hooks::modifyhook($args);
}
