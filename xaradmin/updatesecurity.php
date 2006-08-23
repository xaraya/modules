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
    if( !Security::check(SECURITY_ADMIN, 'security') ){ return false; }

    if( !xarVarFetch('modid',    'id', $modid,     0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemtype', 'id', $itemtype,  0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemid',   'id', $itemid,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('returnurl','str',$returnUrl, '', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('levels','array',$levels, '', XARVAR_NOT_REQUIRED) ){ return false; }

    extract($args);

    $security = new SecurityLevels($modid, $itemtype, $itemid);
    foreach( $levels as $role_id => $level )
    {
        $security->add(new SecurityLevel($level), $role_id);
    }

    $result = Security::update($security, $modid, $itemtype, $itemid);
    if( !$result ){ return false; }

    xarResponseRedirect($returnUrl);
    return false;
}
?>