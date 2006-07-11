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
 * @link http://xaraya.com/index.php/release/50.html
 * @author Chris Dudley <miko@xaraya.com>
 * @author Richard Cave <rcave@xaraya.com>
 * @author Sylvain Beucler <beuc@beuc.net>
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
    $data['anonymousbindvalue'] = xarVarPrepForDisplay($ldap->anonymous_bind);

    // Bind DN (default is 'o=dept')
    $data['binddnvalue'] = xarVarPrepForDisplay($ldap->bind_dn);

    // UID Field (default is 'cn')
    $data['uidfieldvalue'] = xarVarPrepForDisplay($ldap->uid_field);

    // Search user dn (true/false)
    $data['searchuserdnvalue'] = xarVarPrepForDisplay($ldap->search_user_dn);

    // Admin Login
    $data['adminidvalue'] = xarVarPrepForDisplay($ldap->admin_login);

    // Admin password

    // Admin password is encrypted - so decrypt
    $adminpasswd = $ldap->encrypt($ldap->admin_password, 0);
    $data['adminpasswdvalue'] = xarVarPrepForDisplay($adminpasswd);

    // Use TLS - LDAP Protocol 3 only
    $data['tls'] = xarVarPrepForDisplay($ldap->tls);


    // Add user to xar_roles  
    $data['adduservalue'] = xarModGetVar('authldap','add_user');
    
    // Username
    $data['adduserunamevalue'] = xarVarPrepForDisplay(xarModGetVar('authldap','add_user_uname'));

    // User email
    $data['adduseremailvalue'] = xarVarPrepForDisplay(xarModGetVar('authldap','add_user_email'));

    // Failover to local authentication of LDAP fails 
    $data['failovervalue'] = xarVarPrepForDisplay(xarModGetVar('authldap','failover'));

    // Store user's LDAP password in Xaraya database?  
    $data['storepasswordvalue'] = xarVarPrepForDisplay(xarModGetVar('authldap','store_user_password'));

    // Get groups
    $data['defaultgroup'] = xarVarPrepForDisplay(xarModGetVar('authldap', 'defaultgroup'));

    // Get default users group
    if (!isset($data['defaultgroup'])) {
        // See if Users role exists
        if( xarFindRole('Users'))
            $data['defaultgroup'] = 'Users';
    } 

    // Get the list of groups
    if (!$groupRoles = xarGetGroups()) return; // throw back

    $group = array();
    $already_seen = array();
    foreach ($groupRoles as $group) {
      if(!isset($already_seen[$group['uid']])) {
   $groups[] = array('name' => xarVarPrepForDisplay($group['name']),
             'id' => $group['uid']);
   $already_seen[$group['uid']] = 1;
      }
    }
    $data['groups'] = $groups;


    /** LDAP Groups parameters **/

    include_once('modules/authldap/xarincludes/default_variables.php');
    foreach(array_keys($default_groups_variables) as $variable)
      $data[$variable] = xarVarPrepForDisplay(xarModGetVar('authldap', $variable));

    // everything else happens in Template for now
    return $data;
}

?>