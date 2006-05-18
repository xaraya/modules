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
    Updates the security of a module item

    @param

    @return boolean returns false to stop processing for the redirect
*/
function security_admin_updatesecurity($args)
{
    if( !xarVarFetch('modid',    'id', $modid,     0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemtype', 'id', $itemtype,  0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemid',   'id', $itemid,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('returnurl','str',$returnUrl, '', XARVAR_NOT_REQUIRED) ){ return false; }

    if( !xarVarFetch('overview', 'array', $overview,array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('read',     'array', $read,    array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('comment',  'array', $comment, array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('write',    'array', $write,   array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('manage',   'array', $manage,  array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('admin',    'array', $admin,   array(), XARVAR_NOT_REQUIRED) ){ return false; }

    extract($args);

    if( !xarModAPILoad('security', 'user') ){ return false; }

    /*
        If user has SECURITY_ADMIN level or is a site admin let them
        modify security otherwise don't
    */
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

    $secLevels = xarModAPIFunc('security', 'user', 'getlevels');
    $levels = array();

    // Read checks from form and setup the levels for storage
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

    xarResponseRedirect($returnUrl);

    return false;
}
?>