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
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function authldap_admin_modifyconfig()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthLDAP')) return;
    
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    
    // Create LDAP object
    include_once 'modules/xarldap/xarldap.php';
    $ldap = new xarLDAP();

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

    // Admin password

    // Admin password is encrypted - so decrypt
    $adminpasswd = $ldap->encrypt($ldap->admin_password, 0);
    $data['adminpasswdvalue'] = xarVarPrepForDisplay($adminpasswd);

    // Use TLS - LDAP Protocol 3 only
    if ($ldap->tls == 'true') {
        $data['tls'] = xarVarPrepForDisplay("checked");
    } else {
        $data['tls'] = "";
    }

    // Add user to xar_roles
    if (xarModGetVar('authldap','add_user') == 'true') {    
        $data['adduservalue'] = xarVarPrepForDisplay("checked");
    } else {
        $data['adduservalue'] = "";
    }
    
    // Username
    $data['adduserunamevalue'] = xarVarPrepForDisplay(xarModGetVar('authldap','add_user_uname'));

    // User email
    $data['adduseremailvalue'] = xarVarPrepForDisplay(xarModGetVar('authldap','add_user_email'));

    // Failover to local authentication of LDAP fails
    if (xarModGetVar('authldap','failover') == 'true') {    
        $data['failovervalue'] = xarVarPrepForDisplay("checked");
    } else {
        $data['failovervalue'] = "";
    }

    // Store user's LDAP password in Xaraya database?
    if (xarModGetVar('authldap','store_user_password') == 'true') {    
        $data['storepasswordvalue'] = xarVarPrepForDisplay("checked");
    } else {
        $data['storepasswordvalue'] = "";
    }
    
    // Get groups
    $data['defaultgroup'] = xarModGetVar('authldap', 'defaultgroup');

    // Get default users group
    if (!isset($data['defaultgroup'])) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $data['defaultgroup'] = 'Users';
    } 

    // Get the list of groups
    if (!$groupRoles = xarGetGroups()) return; // throw back

    $i=0;
    while (list($key,$group) = each($groupRoles)) {
        $groups[$i]['name'] = xarVarPrepForDisplay($group['name']);
        $i++;
    }
    $data['groups'] = $groups;

    // Submit button
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Submit'));
       
    // everything else happens in Template for now
    return $data;
}

?>
