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
    extract($args);

    xarModAPILoad('security', 'user');

    // setup vars
    $modid = '';
    if( !empty($extrainfo['module']) )
        $modid = xarModGetIdFromName($extrainfo['module']);

    $itemtype = '';
    if( !empty($extrainfo['itemtype']) )
        $itemtype = $extrainfo['itemtype'];

    $itemid = '';
    if( !empty($objectid) )
        $itemid = $objectid;

    $has_admin_security = xarModAPIFunc('security', 'user', 'check',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'itemid'   => $itemid,
            'level'    => SECURITY_ADMIN,
            'hide_exception' => true
        )
    );
    if( !$has_admin_security && !xarSecurityCheck('AdminPanel', 0) ){ return ''; }

    // Do a little poll to see if
    xarVarFetch('user',   'array', $user,   array(), XARVAR_NOT_REQUIRED);
    xarVarFetch('groups', 'array', $groups, array(), XARVAR_NOT_REQUIRED);
    xarVarFetch('world',  'array', $world,  array(), XARVAR_NOT_REQUIRED);
    xarVarFetch('group',  'int',   $group,  0,       XARVAR_NOT_REQUIRED);

    if( $group > 0 )
    {
        $result = xarModAPIFunc('security', 'admin', 'create_group_level',
            array(
                'modid'    => $modid,
                'itemtype' => $itemtype,
                'itemid'   => $itemid,
                'group'    => $group,
                'level'    => SECURITY_READ
            )
        );
    }

    // Calc all new levels
    $userLevel = 0;
    foreach( $user as $part )
        $userLevel += $part;

    $groupsLevel = array();
    foreach( $groups as $key => $group )
    {
        $groupsLevel[$key] = 0;
        foreach( $group as $part )
            $groupsLevel[$key] += $part;
    }

    $worldLevel = 0;
    foreach( $world as $part )
        $worldLevel += $part;

    $settings['levels'] = array(
        'user' => $userLevel,
        'groups' => $groupsLevel,
        'world' => $worldLevel
    );
    $sargs = array(
        'modid'    => $modid,
        'itemtype' => $itemtype,
        'itemid'   => $itemid,
        'settings' => $settings
    );
    xarModAPIFunc('security', 'admin', 'update', $sargs);

    /*

    // Check to see if we have an entry already
    $securityExists = xarModAPIFunc('security', 'user', 'securityexists',
        array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid));

    // If this has not been owned before set ownership to current user
    if( !$securityExists )
    {
       xarModAPIFunc('security', 'admin', 'createhook', $args);
    }
    else
    {
    $sargs = array(
        'modid'    => $modid,
        'itemtype' => $itemtype,
        'itemid'   => $itemid,
        'settings' => $settings
    );
    xarModAPIFunc('security', 'admin', 'create', $sargs);

    }
    */

    return $extrainfo;
}
?>