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
    extract($args);

    if (!isset($uname) || !isset($pass) || $pass == "") {
        $msg = xarML('Empty uname (#(1)) or pass (not shown).', $uname);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }

    $ldapconfig['add_user'] = xarModGetVar('authldap','add_user');
    $ldapconfig['add_user_uname'] = xarModGetVar('authldap','add_user_uname');
    $ldapconfig['add_user_email'] = xarModGetVar('authldap','add_user_email');

    // open ldap connection
    $connect = xarModAPIFunc('authldap',
                             'user',
                             'open_ldap_connection');
    if (!$connect) return XARUSER_AUTH_FAILED;

    // get user information
    $user_info = xarModAPIFunc('authldap',
                               'user',
                               'get_ldap_userdata',
                               array('connect' => $connect,
                                     'uname' => $uname,
                                     'pass' => $pass)); 

    if (!$user_info) return XARUSER_AUTH_FAILED;

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
                // get username from LDAP
                $realname = xarModAPIFunc('authldap',
                                          'user',
                                          'get_attribute_value',
                                          array('connect' => $connect,
                                                'entry' => $user_info,
                                                'attribute' => $ldapconfig['add_user_uname'])); 
            }
            if ($ldapconfig['add_user_email']) {
                // get email from LDAP
                $email = xarModAPIFunc('authldap',
                                          'user',
                                          'get_attribute_value',
                                          array('connect' => $connect,
                                                'entry' => $user_info,
                                                'attribute' => $ldapconfig['add_user_email'])); 
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
            if (!$groupRoles = xarGetGroups()) return; // throw back

            $groupId = 0;
            while (list($key,$group) = each($groupRoles)) {
                if ($group['name'] == $usergroup) { 
                    $groupId = $group['uid'];
                    break;
                }
            }

            if ($groupId == 0) return; // throw back

            // Insert the user into the default users group
            if( !xarMakeRoleMemberByID($rid, $groupId))
               return XARUSER_AUTH_FAILED; 

        } else {
            $rid = XARUSER_AUTH_FAILED;
        }
   } else {
        $rid = $userRole['uid'];
    }

    // close LDAP connection
    $connect = xarModAPIFunc('authldap',
                             'user',
                             'open_ldap_connection',
                             array('connect' => $connect));

    return $rid;
}

?>
