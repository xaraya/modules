<?php
/**
 * File: $Id$
 *
 * XarLDAP Administration
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
 * xarldap_admin_connecttest: 
 *
 * Test the xarldap connection
 *
 * @author  Richard Cave <rcave@xaraya.com>
 * @access  public
 * @param   none 
 * @return  returns true on success or false on failure
 * @throws  none
 * @todo    none
*/
function xarldap_admin_connecttest()
{
    // Security check
    if(!xarSecurityCheck('AdminXarLDAP')) return;
    
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
        $data['anonymousbindvalue'] = xarVarPrepForDisplay("yes");
    } else {
        $data['anonymousbindvalue'] = xarVarPrepForDisplay("no");
    }

    // Bind DN (default is 'o=dept')
    $data['binddnvalue'] = xarVarPrepForDisplay($ldap->bind_dn);

    // UID Field (default is 'cn')
    $data['uidfieldvalue'] = xarVarPrepForDisplay($ldap->uid_field);

    // Search user dn (true/false)
    if ($ldap->search_user_dn == 'true') {    
        $data['searchuserdnvalue'] = xarVarPrepForDisplay("yes");
    } else {
        $data['searchuserdnvalue'] = xarVarPrepForDisplay("no");
    }

    // Admin Login
    $data['adminidvalue'] = xarVarPrepForDisplay($ldap->admin_login);

    // Admin password is encrypted - so decrypt
    $adminpasswd = $ldap->encrypt($ldap->admin_password, 0);
    $data['adminpasswdvalue'] = xarVarPrepForDisplay($adminpasswd);

    // Use TLS - LDAP Protocol 3 only
    if ($ldap->tls == 'true') {    
        $data['tls'] = xarVarPrepForDisplay("yes");
    } else {
        $data['tls'] = xarVarPrepForDisplay("no");
    }

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    
    // Return the template variables defined in this function
    return $data;
}

?>
