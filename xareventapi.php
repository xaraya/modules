<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    If a xaraya item is detected perform a security check out it.

    @param $module   name of module
    @param $itemtype item type (optional)
    @param $itemid   item id of item

    @return boolean true if user has access otherwise false

    @throws exception when user is denied access
*/
function security_eventapi_OnServerRequest($args)
{
    $module = xarRequestGetVar('module');
    $itemtype = xarRequestGetVar('itemtype');
    $itemid = xarRequestGetVar('itemid');
    $catid = xarRequestGetVar('catid');

    if( empty($module) ){ $module = xarModGetName(); }

    $modid = xarModGetIdFromName($module);

    if( xarVarIsCached("modules.security", 'itemtype') )
    {
        $itemtype = xarVarGetCached("modules.security", 'itemtype');
    }

    if( xarVarIsCached("modules.security", 'itemid') )
    {
        $itemid = xarVarGetCached("modules.security", 'itemid');
    }

    if( !empty($catid) && empty($itemid) && xarModIsHooked('security', 'categories') )
    {
        $modid = xarModGetIdFromName('categories');
        $module = 'categories';
        if( empty($itemid) ){ $itemid = $catid; }
    }

    if( !empty($itemid) && xarModIsHooked('security', $module) )
    {
        if( !isset($itemtype) ){ $itemtype = 0; }
        if( !Security::check(SECURITY_READ, $modid, $itemtype, $itemid) )
        {
            if( !xarUserIsLoggedIn() && function_exists("errorhandler_eventapi_log_user_in") )
            {
                errorhandler_eventapi_log_user_in();
            }
            return false;
        }
    }

    return true;
}
?>