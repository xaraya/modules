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
 * fetch ldap userdata
 * @private
 * @author Richard Cave
 * @param args['connect'] open LDAP link connection
 * @param args['uname'] user name of the user
 * @param args['pass'] password of user
 * @returns array
 * @return ldap info on successful authentication, XARUSER_AUTH_FAILED otherwise
 */
function authldap_userapi_get_ldap_userdata($args)
{
    extract($args);

    if (!isset($connect) || !isset($uname) || !isset($pass)) {
        $msg = xarML('Empty connect (#(1)) or uname (#(2)) or pass (not shown).', $connect, $uname, $pass);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }

    $ldapconfig['server'] = xarModGetVar('authldap','server');
    $ldapconfig['bind_dn'] = xarModGetVar('authldap','bind_dn');
    $ldapconfig['uid_field'] = xarModGetVar('authldap','uid_field');
    $ldapconfig['search_user_dn'] = xarModGetVar('authldap','search_user_dn');
    $ldapconfig['admin_login'] = xarModGetVar('authldap','admin_login');
    $ldapconfig['admin_passwd'] = xarModGetVar('authldap','admin_password');
    $ldapconfig['anonymous'] = xarModGetVar('authldap','anonymous_bind');

    if($connect)
    {
        // There are three ways to bind to an LDAP server
        //  - anonymous bind
        //  - admin bind
        //  - user bind
        if ($ldapconfig['anonymous'] == 'true') {
            // anonymous bind allowed
            $sbind=ldap_bind($connect);
            if (!$sbind) {
                error_log("LDAP Error: Bind to " . $ldapconfig['server'] . " has failed");
                return false;
            }
        } else if ($ldapconfig['admin_login'] != "" &&
                   $ldapconfig['admin_passwd'] != "") {
            // no anonymous bind, use admin user
            @$sbind=ldap_bind($connect,
                             $ldapconfig['admin_login'],
                             $ldapconfig['admin_passwd']);
            if (!$sbind) {
                error_log("LDAP Error: Bind to " . $ldapconfig['server'] . " has failed");
                return false;
            }
        }

        if ($ldapconfig['search_user_dn'] == 'true') {
            // Bind to LDAP directory successful so search for user info

            // A resource ID is always returned when using URLs for the 
            // host parameter even if the host does not exist.  This will
            // cause a PHP exception if the host does not exist, so 
            // suppress hard error return as the PHP exception will contain
            // the user's password.
            @$result=ldap_search($connect,$ldapconfig['bind_dn'], $ldapconfig['uid_field']."=". $uname);
            if (!$result) {
                error_log("LDAP Error: Search " . $ldapconfig['server'] . " has failed - no result set");
                return false;
            }
            $ldap_user_info = ldap_get_entries($connect, $result);

            // check if user exists
            if($ldap_user_info['count']==0) return false;
            $user_dn = $ldap_user_info[0]['dn'];
        } else {
            // validate password - @ suppresses hard error return
            // @ gives soft return....
            $user_dn = $ldapconfig['uid_field']."=" . $uname . "," . $ldapconfig['bind_dn'];
        }

        // try to bind with user and password
        @$bind=ldap_bind($connect,$user_dn,$pass);
        if (!$bind) return false;

        // build ldapinfo arrary
        $result=ldap_search($connect,$ldapconfig['bind_dn'], $ldapconfig['uid_field']."=". $uname);
        $ldap_user_info = ldap_get_entries($connect, $result);

         // ldap_get_entries returns true even if no results
         // are found, so check for number of rows in array
         if($ldap_user_info['count']==0) return false;
         
         return $ldap_user_info;
    }
}
?>
