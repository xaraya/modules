<?php
/**
 * AuthLDAP Administrative Display Functions
 * 
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com>
 * @author Richard Cave <rcave@xaraya.com>
 * @author Sylvain Beucler <beuc@beuc.net>
*/

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authldap_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('ldapserver', 'str:1:', $ldapserver, '')) return;
    if (!xarVarFetch('portnumber', 'str:1:', $portnumber, '')) return;
    if (!xarVarFetch('anonymousbind', 'checkbox', $anonymousbind, false)) return;
    if (!xarVarFetch('binddn', 'str:1:', $binddn, '')) return;
    if (!xarVarFetch('uidfield', 'str:1:', $uidfield, '')) return;
    if (!xarVarFetch('searchuserdn', 'checkbox', $searchuserdn, true)) return;
    if (!xarVarFetch('adminid', 'str:1:', $adminid, '')) return;
    if (!xarVarFetch('adminpasswd', 'str:1:', $adminpasswd, '')) return;
    if (!xarVarFetch('tls', 'checkbox', $tls, false)) return;
    if (!xarVarFetch('activate', 'checkbox', $activate, true)) return;
    if (!xarVarFetch('failover', 'checkbox', $failover, true)) return;
    if (!xarVarFetch('adduser', 'checkbox', $adduser, true)) return;
    if (!xarVarFetch('storepassword', 'checkbox', $storepassword, true)) return;
    if (!xarVarFetch('adduseruname', 'str:1:', $adduseruname, 'sn')) return;
    if (!xarVarFetch('adduseremail', 'str:1:', $adduseremail, 'mail')) return;
    if (!xarVarFetch('defaultgroup', 'str:1:', $defaultgroup, '')) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Create a new ldap object
    include_once 'modules/xarldap/xarldap.php';
    $ldap = new xarLDAP();

    // Update the xarldap settings
    // update the data
    if(!$searchuserdn){
        $ldap->set_variable('search_user_dn', 'false');
    } else {
        $ldap->set_variable('search_user_dn', 'true');
    }

    $ldap->set_variable('server', $ldapserver);
    $ldap->set_variable('bind_dn', $binddn);
    $ldap->set_variable('uid_field', $uidfield);
    $ldap->set_variable('port_number', $portnumber);
    $ldap->set_variable('admin_login', $adminid);

    if (!empty($adminpasswd)) {
        // Generate a key for password encrpytion
        $key = $ldap->generate_key(time());

        // Save the key
        $ldap->set_variable('key', $key);

        // Encrypt the admin password
        $password = $ldap->encrypt($adminpasswd);

        $ldap->set_variable('admin_password', $password);
    } else {
        $ldap->set_variable('admin_password', '');
        $ldap->set_variable('key', '');
    }

    if(!$anonymousbind) {
        $ldap->set_variable('anonymous_bind', 'false');
    } else {
        $ldap->set_variable('anonymous_bind', 'true');
    }

    if(!$tls){
        $ldap->set_variable('tls', 'false');
    } else {
        $ldap->set_variable('tls', 'true');
    }

    // Update the authldap settings
    if(!$adduser) {
        xarModSetVar('authldap', 'add_user', 'false');
    } else {
        xarModSetVar('authldap', 'add_user', 'true');
    }
    xarModSetVar('authldap', 'add_user_uname', $adduseruname);
    xarModSetVar('authldap', 'add_user_email', $adduseremail);

    if(!$storepassword) {
        xarModSetVar('authldap', 'store_user_password', 'false');
    } else {
        xarModSetVar('authldap', 'store_user_password', 'true');
    }

    if(!$failover) {
        xarModSetVar('authldap', 'failover', 'false');
    } else {
        xarModSetVar('authldap', 'failover', 'true');
    }


    // Get default users group
    if (empty($defaultgroup)) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $defaultgroup = 'Users';
    } 
    xarModSetVar('authldap', 'defaultgroup', $defaultgroup);


    // Groups variables
    include_once('modules/authldap/xarincludes/default_variables.php');
    foreach (array_keys($default_groups_variables) as $variable) {
        unset($value); // important! else varVarFetch won't assign the $value
        if (!xarVarFetch($variable, 'str:1:', $value, '')) return;
        xarModSetVar('authldap', $variable, $value);
    }

    $authmodules = xarConfigGetVar('Site.User.AuthenticationModules');
    if (empty($activate) && in_array('authldap', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module != 'authldap') {
                $newauth[] = $module;
            }
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    } elseif (!empty($activate) && !in_array('authldap', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module == 'authsystem') {
                $newauth[] = 'authldap';
            }
            $newauth[] = $module;
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    }

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('authldap', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>