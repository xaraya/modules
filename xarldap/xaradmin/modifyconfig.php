<?php
/**
 * File: $Id$
 *
 * XarLDAP Administrative Display Functions
 * 
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage xarldap
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function xarldap_admin_modifyconfig()
{
    // Security check
    if(!xarSecurityCheck('AdminXarLDAP')) return;
    
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    
    // Create LDAP object
    include_once 'modules/xarldap/xarldap.php';
    $ldap = new xarldap();

    // Set the LDAP object parameters from module variables
    $ldap->get_parameters(); 

    // Server (default is '127.0.0.1')
    $data['ldapservervalue'] = xarVarPrepForDisplay($ldap->server);

    // Port number
    $data['portnumbervalue'] = xarVarPrepForDisplay($ldap->port_number);

    // Allow anonymous bind to server (true/false)
    if ($ldap->anonymous_bind == 'true') {    
        $data['anonymousbindvalue'] = xarVarPrepForDisplay("checked");
    } else {
        $data['anonymousbindvalue'] = "";
    }

    // Bind DN (default is 'o=dept')
    $data['binddnvalue'] = xarVarPrepForDisplay($ldap->bind_dn);

    // UID Field (default is 'cn')
    $data['uidfieldvalue'] = xarVarPrepForDisplay($ldap->uid_field);

    // Search user dn (true/false)
    if ($ldap->search_user_dn == 'true') {    
        $data['searchuserdnvalue'] = xarVarPrepForDisplay("checked");
    } else {
        $data['searchuserdnvalue'] = "";
    }

    // Admin Login
    $data['adminidvalue'] = xarVarPrepForDisplay($ldap->admin_login);

    // Admin password is encrypted - so decrypt
    $adminpasswd = $ldap->encrypt($ldap->admin_password, 0);
    $data['adminpasswdvalue'] = xarVarPrepForDisplay($adminpasswd);

    // Use TLS - LDAP Protocol 3 only
    if ($ldap->tls == 'true') {    
        $data['tls'] = xarVarPrepForDisplay("checked");
    } else {
        $data['tls'] = "";
    }

    // everything else happens in Template for now
    return $data;
}

?>
