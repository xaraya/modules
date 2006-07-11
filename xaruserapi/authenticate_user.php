<?php
/**
 * AuthSSO User API
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSSO
 * @link http://xaraya.com/index.php/release/51.html
 * @author Jonn Beames and Richard Cave
 */

/**
 * Login an externally authenticated user
 * @public
 * @author Jonn Beames | Richard Cave
 * @param args['uname'] username of user
 * @param args['pass'] dummy password
 * @returns int
 * @return uid on successful login, XARUSER_AUTH_FAILED otherwise
 * @todo rework to make external user info access easily configurable, including LDAP
 */
function authsso_userapi_authenticate_user($args)
{
    extract($args);
    // Don't use authsso for designated admin
    $roles = new xarRoles();
    $adminuser = $roles->getRole(xarModGetVar('roles','admin'));
    $adminname = $adminuser->getUname();
    if ($uname == $adminname) {
        return XARUSER_AUTH_FAILED;
    } else {
        $uname ='';
    }

    // get the name of user authenticated by server
    if (!empty($_SERVER['REMOTE_USER'])) {
        $uname = xarServerGetVar('REMOTE_USER');
        while (strpos($uname, '\\') != false) {
            // if domain name is included with the username, remove it
            // Todo: support multiple NT domains with different email domains
            $dlength = (strpos($uname, '\\') + 1);
            $uname = substr($uname, $dlength);
        }
        $pass = 'SSOnotUsed'; // A random password is generated for new users
    } else {
        return XARUSER_AUTH_FAILED;
    }

    // get the config variables
    $ssoconfig = array();
    $ssoconfig['add_user'] = xarModGetVar('authsso', 'add_user');
    $ssoconfig['add_user_maildomain'] = xarModGetVar('authsso', 'add_user_maildomain');
    $ssoconfig['getfromldap'] = xarModGetVar('authsso', 'getfromldap');
    $ssoconfig['ldapdisplayname'] = xarModGetVar('authsso', 'ldapdisplayname');
    $ssoconfig['ldapmail'] = xarModGetVar('authsso', 'ldapmail');

    // match the remote_user to a xaraya uname
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Get user information from roles
    $userRole = xarModAPIFunc('roles', 'user', 'get', array('uname' => $uname));

    if (!$userRole) {
        // add a user that does NOT exist in the database
        // Todo: make external data source access extensible and configurable
        if ($ssoconfig['add_user']) {
            $pass = '';
            $realname = '';
            $email = '';

            if (($ssoconfig['getfromldap']) && (xarModIsAvailable('xarldap') == 'true')) {

                // Include xarldap class
                include_once 'modules/xarldap/xarldap.php';

                $ldap = new xarLDAP();
                $ldap->open();
                $bindResult = $ldap->bind_to_server();
                $user_dn = $ldap->search_user_dn($uname);
                $searchResult=$ldap->search($ldap->bind_dn, $ldap->uid_field."=". $uname);
                $userInfo = $ldap->get_entries($searchResult);
                $ldap->close();
                if ($ssoconfig['ldapdisplayname']) {
                    // get username from LDAP user info
                    $realname = $ldap->get_attribute_value($userInfo, $ssoconfig['ldapdisplayname']);
                    if (empty($realname)) {
                        $realname = $uname;
                    }
                }
                if ($ssoconfig['ldapmail']) {
                    // get email from LDAP user info
                    $email = $ldap->get_attribute_value($userInfo, $ssoconfig['ldapmail']);
                    if (empty($email)) {
                        $email = $uname . xarML('LDAP_RETRIEVAL_FAILED');
                    }
                }
                $valcode = 'createdbyldap';

            } else {
                $realname = $uname;
                $email = $uname .'@'. $ssoconfig['add_user_maildomain'];
                $valcode = 'createdbysso';
            }

            // call role module to create new user role
            $now = time();
            $pass = xarModAPIFunc('roles', 'user', 'makepass');

            $rid = xarModAPIFunc('roles', 'admin', 'create',
                                 array('uname' => $uname,
                                       'realname' => $realname,
                                       'email' => $email,
                                       'pass' => $pass, // not used by authsso
                                       'date' => $now,
                                       'valcode' => $valcode,
                                       'state' => 3,
                                       'authmodule' => 'authsso'));

            if (!$rid) {
                return XARUSER_AUTH_FAILED;
            }

            $usergroup = xarModGetVar('authsso', 'defaultgroup');

            // Get the list of groups
            if (!$groupRoles = xarGetGroups()) {
                return XARUSER_AUTH_FAILED;
            }

            $groupId = 0;
            while (list($key,$group) = each($groupRoles)) {
                if ($group['name'] == $usergroup) {
                    $groupId = $group['uid'];
                    break;
                }
            }

            if ($groupId == 0) {
                return XARUSER_AUTH_FAILED;
            }

            // Insert the user into the default users group
            if ( !xarMakeRoleMemberByID($rid, $groupId)) {
               return XARUSER_AUTH_FAILED;
            }

        } else {
            $rid = XARUSER_AUTH_FAILED;
        }
    } else {
        $rid = $userRole['uid'];
    }

    // At the begining of the server reqest the session was for an anonymous user,
    // now it's not, flush any security cache that may have accumulated thus far.
    xarVarFlushCached('Security.Variables');
    return $rid;
}

?>