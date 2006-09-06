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
    Creates default security for an item if none exists

    @param $args array standard hook params

    @return $extrainfo array containing standard hooks extrainfo
*/
function security_adminapi_updatehook($args)
{
    // This func is no longer used. Thanks to ajax this is not needed now.
    extract($args);
    return $extrainfo;

    // setup vars
//    $modid = 0;
//    if( !empty($extrainfo['module']) )
//        $modid = xarModGetIdFromName($extrainfo['module']);
//
//    $itemtype = 0;
//    if( !empty($extrainfo['itemtype']) )
//        $itemtype = $extrainfo['itemtype'];
//
//    $itemid = 0;
//    if( !empty($objectid) )
//        $itemid = $objectid;
//
//    if( !Security::check(SECURITY_ADMIN, $modid, $itemtype, $itemid, false) ){ return ''; }
//
//    if( !xarVarFetch('levels','array',$levels, '', XARVAR_NOT_REQUIRED) ){ return false; }
//
//    $security = new SecurityLevels($modid, $itemtype, $itemid);
//    foreach( $levels as $role_id => $level )
//    {
//        $security->add(new SecurityLevel($level), $role_id);
//    }
//
//    $result = Security::update($security, $modid, $itemtype, $itemid);
//    if( !$result ){ return false; }
//
//    return $extrainfo;
}
?>