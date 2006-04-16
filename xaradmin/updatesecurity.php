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
    xarVarFetch('modid',    'id', $modid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype', 'id', $itemtype, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemid',   'id', $itemid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('returnurl','str',$returnUrl, '', XARVAR_NOT_REQUIRED);

    xarVarFetch('user',   'array', $user,   array(), XARVAR_NOT_REQUIRED);
    xarVarFetch('groups', 'array', $groups, array(), XARVAR_NOT_REQUIRED);
    xarVarFetch('world',  'array', $world,  array(), XARVAR_NOT_REQUIRED);

    extract($args);

    xarModAPILoad('security', 'user');

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

    xarResponseRedirect($returnUrl);

    return false;
}
?>