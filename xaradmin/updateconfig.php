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
    list($ldapserver,
         $portnumber,
         $anonymousbind,
         $binddn,
         $uidfield,
         $searchuserdn,
         $adminid,
         $adminpasswd,
         $tls) = xarVarCleanFromInput('ldapserver', 
                                      'portnumber', 
                                      'anonymousbind', 
                                      'binddn', 
                                      'uidfield', 
                                      'searchuserdn', 
                                      'adminid', 
                                      'adminpasswd',
                                      'tls');

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
