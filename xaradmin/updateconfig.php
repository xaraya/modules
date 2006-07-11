<?php
/**
 * AuthSSO Administrative Display Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSSO
 * @link http://xaraya.com/index.php/release/51.html
 * @author Jonn Beames <jsb@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authsso_admin_updateconfig()
{
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    
    // Security check
    if(!xarSecurityCheck('AdminAuthSSO')) return;
    
    // Get parameters
    if (!xarVarFetch('adduser', 'checkbox', $adduser, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maildomain', 'str:1:64', $maildomain, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useldap', 'checkbox', $useldap, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ldapnamevalue', 'str:1:64', $ldapnamevalue, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ldapmailvalue', 'str:1:64', $ldapmailvalue, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultgroup', 'str:1:64', $defaultgroup, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('activate', 'str', $activate, '', XARVAR_NOT_REQUIRED)) return;

    // update the config
    xarModSetVar('authsso', 'add_user', $adduser);
    xarModSetVar('authsso', 'getfromldap', $useldap);
    xarModSetVar('authsso', 'add_user_maildomain', $maildomain);
    xarModSetVar('authsso', 'ldapdisplayname', $ldapnamevalue);
    xarModSetVar('authsso', 'ldapmail', $ldapmailvalue);

    // Get default users group
    if (empty($defaultgroup)) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $defaultgroup = 'Users';
    }
    // update config
    xarModSetVar('authsso', 'defaultgroup', $defaultgroup);

    $authmodules = xarConfigGetVar('Site.User.AuthenticationModules');
    if (empty($activate) && in_array('authsso', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module != 'authsso') {
                $newauth[] = $module;
            }
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    } elseif (!empty($activate) && !in_array('authsso', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module == 'authsystem') {
                $newauth[] = 'authsso';
            }
            $newauth[] = $module;
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    }

    // and refresh display
    xarResponseRedirect(xarModURL('authsso', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>