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
    
    // prepare labels and values for display by the template
    $data['title'] = xarVarPrepForDisplay(xarML('Administration'));
    $data['configadmin'] = xarVarPrepForDisplay(xarML('Configure XarLDAP'));

    // Create LDAP object
    include_once 'modules/xarldap/xarldap.php';
    $ldap = new xarLDAP();

    // Set the LDAP object parameters from module variables
    $ldap->get_parameters(); 

    // Server (default is '127.0.0.1')
    $data['ldapserver'] = xarVarPrepForDisplay(xarML('LDAP Server Name or IP'));
    $data['ldapservervalue'] = xarVarPrepForDisplay($ldap->server);

    // Port number
    $data['portnumber'] = xarVarPrepForDisplay(xarML('LDAP Server Port Number'));
    $data['portnumbervalue'] = xarVarPrepForDisplay($ldap->port_number);

    // Allow anonymous bind to server (true/false)
    $data['anonymousbind'] = xarVarPrepForDisplay(xarML('Anonymously Bind to Server'));
    if ($ldap->anonymous_bind == 'true') {    
        $data['anonymousbindvalue'] = xarVarPrepForDisplay("checked");
    } else {
        $data['anonymousbindvalue'] = "";
    }

    // Bind DN (default is 'o=dept')
    $data['binddn'] = xarVarPrepForDisplay(xarML('LDAP bind DN'));
    $data['binddnvalue'] = xarVarPrepForDisplay($ldap->bind_dn);

    // UID Field (default is 'cn')
    $data['uidfield'] = xarVarPrepForDisplay(xarML('LDAP UserID Field Name'));
    $data['uidfieldvalue'] = xarVarPrepForDisplay($ldap->uid_field);

    // Search user dn (true/false)
    $data['searchuserdn'] = xarVarPrepForDisplay(xarML('Search User DN'));
    if ($ldap->search_user_dn == 'true') {    
    $data['searchuserdnvalue'] = xarVarPrepForDisplay("checked");
    } else {
    $data['searchuserdnvalue'] = "";
    }

    // Admin Login
    $data['adminid'] = xarVarPrepForDisplay(xarML('LDAP Admin ID'));
    $data['adminidvalue'] = xarVarPrepForDisplay($ldap->admin_login);

    // Admin password
    $data['adminpasswd'] = xarVarPrepForDisplay(xarML('LDAP Admin Password'));

    // Admin password is encrypted - so decrypt
    $adminpasswd = $ldap->encrypt($ldap->admin_password, 0);

    $data['adminpasswdvalue'] = xarVarPrepForDisplay($adminpasswd);

    // Submit button
    $data['submitlabel'] = xarVarPrepForDisplay(xarML('Click "Submit" to change configuration:'));
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Submit'));

    // everything else happens in Template for now
    return $data;
}

?>
