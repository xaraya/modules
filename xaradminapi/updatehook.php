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
    $modid = 0;
    if( !empty($extrainfo['module']) )
        $modid = xarModGetIdFromName($extrainfo['module']);

    $itemtype = 0;
    if( !empty($extrainfo['itemtype']) )
        $itemtype = $extrainfo['itemtype'];

    $itemid = 0;
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
    if( !$has_admin_security ){ return ''; }

    // Do a little poll to see if
    if( !xarVarFetch('group',  'int',   $group,  0,       XARVAR_NOT_REQUIRED) ){ return false; }

    if( !xarVarFetch('overview', 'array', $overview,array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('read',     'array', $read,    array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('comment',  'array', $comment, array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('write',    'array', $write,   array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('manage',   'array', $manage,  array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('admin',    'array', $admin,   array(), XARVAR_NOT_REQUIRED) ){ return false; }

    if( $group > 0 )
    {
        $result = xarModAPIFunc('security', 'admin', 'create_group_level',
            array(
                'modid'    => $modid,
                'itemtype' => $itemtype,
                'itemid'   => $itemid,
                'group'    => $group,
                'level'    => array('read' => 1)
            )
        );
    }

    $secLevels = xarModAPIFunc('security', 'user', 'getlevels');
    $levels = array();

    // Calc all new levels
    foreach( $secLevels as $secLevel )
    {
        foreach( $$secLevel['name'] as $role_id => $value )
        {
            $levels[$role_id][$secLevel['name']] = $value;
        }
    }

    $settings['levels'] = $levels;
    $sargs = array(
        'modid'    => $modid,
        'itemtype' => $itemtype,
        'itemid'   => $itemid,
        'settings' => $settings
    );
    xarModAPIFunc('security', 'admin', 'update', $sargs);

    return $extrainfo;
}
?>