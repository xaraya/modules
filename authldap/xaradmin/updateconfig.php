<?php
/**
 * File: $Id$
 *
 * AuthLDAP Administrative Display Functions
 * 
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authldap_admin_updateconfig()
{
    // Get parameters
    list($ldapserver,
         $portnumber,
         $anonymousbind,
         $binddn,
         $uidfield,
         $searchuserdn,
         $adminid,
         $adminpasswd,
         $tls,
         $adduser,
         $adduseruname,
         $adduseremail,
         $storepassword,
         $failover,
         $defaultgroup ) = xarVarCleanFromInput('ldapserver',
                                                'portnumber',
                                                'anonymousbind',
                                                'binddn',
                                                'uidfield',
                                                'searchuserdn',
                                                'adminid',
                                                'adminpasswd',
                                                'tls',
                                                'adduser', 
                                                'adduseruname', 
                                                'adduseremail', 
                                                'storepassword', 
                                                'failover', 
                                                'defaultgroup');

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
    if (!isset($defaultgroup)) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $defaultgroup = 'Users';
    } 
    xarModSetVar('authldap', 'defaultgroup', $defaultgroup);

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('authldap', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
