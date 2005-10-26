<?php
/**
 *
 * XarLDAP Administrative Display Functions
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage xarldap
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * xarldap_admin_updateconfig 
 *
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 *
 * @author Richard Cave
 * @access public
 * @param  'ldapserver'
 * @param  'portnumber'
 * @param  'anonymousbind'
 * @param  'binddn'
 * @param  'uidfield'
 * @param  'searchuserdn'
 * @param  'adminid'
 * @param  'adminpasswd'
 * @param  'tls'
 * @return array containing the menulinks for the main menu items.
 * @throws none
 * @todo   none
 */
/**
 */
function xarldap_admin_updateconfig()
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

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Create a new ldap object
    include_once 'modules/xarldap/xarldap.php';
    $ldap = new xarldap();

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
 
    if(!$anonymousbind){
        $ldap->set_variable('anonymous_bind', 'false');
    } else {
        $ldap->set_variable('anonymous_bind', 'true');
    }
    
    if(!$tls){
        $ldap->set_variable('tls', 'false');
    } else {
        $ldap->set_variable('tls', 'true');
    }
    
    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('xarldap', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
