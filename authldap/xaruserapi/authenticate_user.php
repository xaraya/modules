<?php
/**
 * File: $Id$
 * 
 * AuthLDAP User API
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
 * authenticate a user
 * @public
 * @author Richard Cave
 * @param args['uname'] user name of user
 * @param args['pass'] password of user
 * @returns int
 * @return uid on successful authentication, XARUSER_AUTH_FAILED otherwise
 */
function authldap_userapi_authenticate_user($args)
{
    // Extract args
    extract($args);

    if (!isset($uname) || !isset($pass) || $pass == "") {
        $msg = xarML('Empty uname (#(1)) or pass (not shown).', $uname);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }

    // Include xarldap class
    include_once 'modules/xarldap/xarldap.php';
    
    // Get module variables and set in array
    $ldapconfig = array();
    $ldapconfig['add_user'] = xarModGetVar('authldap','add_user');
    $ldapconfig['add_user_uname'] = xarModGetVar('authldap','add_user_uname');
    $ldapconfig['add_user_email'] = xarModGetVar('authldap','add_user_email');

    // Create new LDAP object
    $ldap = new xarLDAP();

    // Make sure LDAP extension exists
    if (!$ldap->exists())
        return XARUSER_AUTH_FAILED;

    // Open ldap connection
    if (!$ldap->open())
        return XARUSER_AUTH_FAILED;

    // Bind to LDAP server
   $bindResult = $ldap->bind_to_server();
    if (!$bindResult)
        return XARUSER_AUTH_FAILED;

    // Bind to LDAP directory successful so search for user info
    $user_dn = $ldap->search_user_dn($uname);
    if (!$user_dn)
        return XARUSER_AUTH_FAILED;

    // Try to bind with user and password
   $bindResult = $ldap->bind($user_dn, $pass);
    if (!$bindResult)
        return XARUSER_AUTH_FAILED;

    // Search for user information
    $searchResult=$ldap->search($ldap->bind_dn, $ldap->uid_field."=". $uname);
    if (!$searchResult) 
        return XARUSER_AUTH_FAILED;

    $userInfo = $ldap->get_entries($searchResult);
    if (!$userInfo) 
        return XARUSER_AUTH_FAILED;

    // ldap_get_entries returns true even if no results
    // are found, so check for number of rows in array
    if($userInfo['count']==0)
        return XARUSER_AUTH_FAILED;
     
    // close LDAP connection
    $ldap->close();

    // OK, authentication worked
    // now we still have to fetch the $uid for return

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Get user information from roles
    $userRole = xarModAPIFunc('roles',
                              'user',
                              'get',
                              array('uname' => $uname)); 

    if (!$userRole) {
        // add a user that does NOT exist in the database
        if ($ldapconfig['add_user'] == 'true') {
            $realname = "";
            $email = "";
            if ($ldapconfig['add_user_uname']) {
                // get username from LDAP user info
                $realname = $ldap->get_attribute_value($userInfo, $ldapconfig['add_user_uname']);
            }

            if ($ldapconfig['add_user_email']) {
                // get email from LDAP
                $email = $ldap->get_attribute_value($userInfo, $ldapconfig['add_user_email']);
            }

            // call role module to create new user role
            $now = time();
            $rid = xarModAPIFunc('roles',
                                 'admin',
                                 'create',
                                 array('uname' => $uname, 
                                       'realname' => $realname, 
                                       'email' => $email, 
                                       'pass' => $pass,
                                       'date'     => $now,
                                       'valcode'  => 'createdbyldap',
                                       'state'   => 3,
                                       'authmodule'  => 'authldap'));

            if (!$rid)
                return XARUSER_AUTH_FAILED;

            $usergroup = xarModGetVar('authldap','defaultgroup');

            // Get the list of groups
            if (!$groupRoles = xarGetGroups()) 
                return XARUSER_AUTH_FAILED;

            $groupId = 0;
            while (list($key,$group) = each($groupRoles)) {
                if ($group['name'] == $usergroup) { 
                    $groupId = $group['uid'];
                    break;
                }
            }

            if ($groupId == 0)
                return XARUSER_AUTH_FAILED;

            // Insert the user into the default users group
            if( !xarMakeRoleMemberByID($rid, $groupId))
               return XARUSER_AUTH_FAILED; 

        } else {
            $rid = XARUSER_AUTH_FAILED;
        }
    } else {
        $rid = $userRole['uid'];
    }

    return $rid;
}

?>
