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
        $binddn,
        $uidfield,
        $searchuserdn,
        $adminid,
        $adminpasswd,
        $portnumber,
        $anonymousbind,
        $adduser,
        $adduseruname,
        $adduseremail,
        $defaultgroup ) = xarVarCleanFromInput('ldapserver',
                                              'binddn', 
                                              'uidfield', 
                                              'searchuserdn', 
                                              'adminid', 
                                              'adminpasswd', 
                                              'portnumber', 
                                              'anonymousbind', 
                                              'adduser', 
                                              'adduseruname', 
                                              'adduseremail', 
                                              'defaultgroup');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // update the data
    if(!$searchuserdn){
        xarModSetVar('authldap', 'search_user_dn', 'false');
    } else {
        xarModSetVar('authldap', 'search_user_dn', 'true');
    }
    
    xarModSetVar('authldap', 'server', $ldapserver);
    xarModSetVar('authldap', 'bind_dn', $binddn);
    xarModSetVar('authldap', 'uid_field', $uidfield);
    xarModSetVar('authldap', 'admin_login', $adminid);
    xarModSetVar('authldap', 'admin_password', $adminpasswd);
    xarModSetVar('authldap', 'port_number', $portnumber);

    if(!$anonymousbind){
        xarModSetVar('authldap', 'anonymous_bind', 'false');
    } else {
        xarModSetVar('authldap', 'anonymous_bind', 'true');
    }
    
    if(!$adduser){
        xarModSetVar('authldap', 'add_user', 'false');
    } else {
        xarModSetVar('authldap', 'add_user', 'true');
    }
    xarModSetVar('authldap', 'add_user_uname', $adduseruname);
    xarModSetVar('authldap', 'add_user_email', $adduseremail);

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
