<?php
/**
 * File: $Id$
 * 
 * AuthLDAP User API
 * 
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * check whether this module has a certain capability
 * @public
 * @param args['capability'] the capability to check for
 * @author Marco Canini
 * @returns bool
 */
function authldap_userapi_has_capability($args)
{
    extract($args);

    if (!isset($capability)) {
        $msg = xarML('Empty capability.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    switch($capability) {
        case XARUSER_AUTH_FAILED:
            return true;
            break;
        case XARUSER_AUTH_DYNAMIC_USER_DATA_HANDLER:
        case XARUSER_AUTH_USER_ENUMERABLE:
        case XARUSER_AUTH_PERMISSIONS_OVERRIDER:
        case XARUSER_AUTH_USER_CREATEABLE:
        case XARUSER_AUTH_USER_DELETEABLE:
            return false;
            break;
    }
    $msg = xarML('Unknown capability.');
    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                   new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    return;
}

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

    $ldapconfig["add_user"] = xarModGetVar('authldap','add_user');
    $ldapconfig["add_user_uname"] = xarModGetVar('authldap','add_user_uname');
    $ldapconfig["add_user_email"] = xarModGetVar('authldap','add_user_email');

    // open ldap connection
    $connect = authldap__open_ldap_connection();
    if (!$connect) return XARUSER_AUTH_FAILED;

    // get user information
    $user_info = authldap__get_ldap_userdata($connect,$uname,$pass);
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
        if ($ldapconfig["add_user"] == 'true') {
            $realname = "";
            $email = "";
            if ($ldapconfig["add_user_uname"]) {
                // get username from LDAP
                $realname = authldap__get_attribute_value($connect,$user_info,$ldapconfig["add_user_uname"]);
            }
            if ($ldapconfig["add_user_email"]) {
                // get email from LDAP
                $email = authldap__get_attribute_value($connect,$user_info,$ldapconfig["add_user_email"]);
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
    authldap__close_ldap_connection($connect);

    return $rid;
}

/**
 * check whether a user variable is avaiable from this module (currently unused)
 * @public
 * @author Marco Canini
 * @returns boolean
 */
function authldap_userapi_is_valid_variable($args)
{
// TODO: differentiate between read & update - might be different

    // ...some way to check if variable is valid...

    // Authsystem can handle all user variables
    return true;
}

/**
 * get a user variable (currently unused)
 * @public
 * @author Marco Canini
 * @param args['uid'] user id
 * @param args['name'] variable name
 * @returns string
 */
function authldap_userapi_get_user_variable($args)
{
    // Second level cache
    static $vars = array();

    extract($args);

    if (!isset($uid) || !isset($name)) {
        $msg = xarML('Empty uid (#(1)) or name (#(2))', $uid, $name);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!isset($vars[$uid])) {
        $vars[$uid] = array();
    }

    if (!isset($vars[$uid][$name])) {
        $vars[$uid][$name] = false;

        // ... retrieve the user variable somehow ...

        // throw back an exception if the user doesn't exist
        //if (...) {
        //    $msg = xarML('User identified by uid #(1) doesn\'t exist.', $uid);
        //    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
        //                  new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        //    return;
        //}

        // $vars[$uid][$name] = $value;
    }

    // Return the variable
    if (isset($vars[$uid][$name])) {
        return $vars[$uid][$name];
    } else {
        return false;
    }
}

/**
 * set a user variable (currently unused)
 * @public
 * @author Gregor J. Rothfuss
 * @param args['uid'] user id
 * @param args['name'] variable name
 * @param args['value'] variable value
 * @returns bool
 */
function authldap_userapi_set_user_variable($args)
{
    extract($args);

    if (!isset($uid) || !isset($name) || !isset($value)) {
        $msg = xarML('Empty uid (#(1)) or name (#(2)) or value (#(3)).', $uid, $name, $value);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // ...update the user variable in the external auth system if applicable...

    // throw back an exception if the user doesn't exist
    //if (...) {
    //    $msg = xarML('User identified by uid #(1) doesn\'t exist.', $uid);
    //    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
    //                  new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    //    return;
    //}

    return true;
}


/*
function authldap_userapi_get_all_users($args)
{

}
*/

/*
function authldap_userapi_get_authorization_info($args)
{

}
*/

/*
function authldap_userapi_create_user($args)
{

}
*/

/*
function authldap_userapi_delete_user($args)
{

}
*/

/**
 * utility function pass individual menu items to the main menu
 * @public
 * @author Richard Cave
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function authldap_userapi_getmenulinks()
{
    // No menu links for users
    $menulinks = '';
    return $menulinks;
}

// PRIVATE FUNCTIONS

/**
 * open ldap connection
 * @private
 * @author Richard Cave
 * @returns int
 * @return LDAP link identifier on connect, false otherwise
 */
function authldap__open_ldap_connection()
{
    // Make sure that LDAP is available before trying to connect
    if (!function_exists('ldap_connect'))
        return;

    $ldapconfig["server"] = xarModGetVar('authldap','server');
    $ldapconfig["portnumber"] = xarModGetVar('authldap','port_number');

    if ($ldapconfig["portnumber"])
        $connect=ldap_connect($ldapconfig["server"],$ldapconfig["portnumber"]);
    else {
        // connect to default port 389
        $connect=ldap_connect($ldapconfig["server"]);
    }

    if (!$connect) {
        error_log("LDAP Error: Connection to " . $ldapconfig['server'] . "failed");
        return false;
    }

    return $connect;
}

/**
 * close ldap connection
 * @private
 * @author Richard Cave
 * @param args['connect'] open LDAP link connection
 * @returns int
 * @return true on success, false otherwise
 */
function authldap__close_ldap_connection($connect)
{
    return ldap_close($connect);
}

/**
 * fetch ldap userdata
 * @private
 * @author Richard Cave
 * @param args['connect'] open LDAP link connection
 * @param args['uname'] user name of user
 * @param args['pass'] password of user
 * @returns int
 * @return uid on successful authentication, XARUSER_AUTH_FAILED otherwise
 */
function authldap__get_ldap_userdata($connect,$uid,$pass)
{
    $ldapconfig["server"] = xarModGetVar('authldap','server');
    $ldapconfig["bind_dn"] = xarModGetVar('authldap','bind_dn');
    $ldapconfig["uid_field"] = xarModGetVar('authldap','uid_field');
    $ldapconfig["search_user_dn"] = xarModGetVar('authldap','search_user_dn');
    $ldapconfig["admin_login"] = xarModGetVar('authldap','admin_login');
    $ldapconfig["admin_passwd"] = xarModGetVar('authldap','admin_password');
    $ldapconfig["anonymous"] = xarModGetVar('authldap','anonymous_bind');

    if($connect)
    {
        // There are three ways to bind to an LDAP server
        //  - anonymous bind
        //  - admin bind
        //  - user bind
        if ($ldapconfig["anonymous"] == 'true') {
            // anonymous bind allowed
            $sbind=ldap_bind($connect);
            if (!$sbind) {
                error_log("LDAP Error: Bind to " . $ldapconfig['server'] . " has failed");
                return false;
            }
        } else if ($ldapconfig["admin_login"] != "" &&
                   $ldapconfig["admin_passwd"] != "") {
            // no anonymous bind, use admin user
            @$sbind=ldap_bind($connect,
                             $ldapconfig["admin_login"],
                             $ldapconfig["admin_passwd"]);
            if (!$sbind) {
                error_log("LDAP Error: Bind to " . $ldapconfig['server'] . " has failed");
                return false;
            }
        }

        if ($ldapconfig["search_user_dn"] == 'true') {
            // Bind to LDAP directory successful so search for user info

            // A resource ID is always returned when using URLs for the 
            // host parameter even if the host does not exist.  This will
            // cause a PHP exception if the host does not exist, so 
            // suppress hard error return as the PHP exception will contain
            // the user's password.
            @$result=ldap_search($connect,$ldapconfig["bind_dn"], $ldapconfig["uid_field"]."=". $uid);
            if (!$result) {
                error_log("LDAP Error: Search " . $ldapconfig['server'] . " has failed - no result set");
                return false;
            }
            $ldap_user_info = ldap_get_entries($connect, $result);

            // check if user exists
            if($ldap_user_info["count"]==0) return false;
            $user_dn = $ldap_user_info[0]["dn"];
        } else {
            // validate password - @ suppresses hard error return
            // @ gives soft return....
            $user_dn = $ldapconfig["uid_field"]."=" . $uid . "," . $ldapconfig['bind_dn'];
        }

        // try to bind with user and password
        @$bind=ldap_bind($connect,$user_dn,$pass);
        if (!$bind) return false;

        // build ldapinfo arrary
        $result=ldap_search($connect,$ldapconfig['bind_dn'], $ldapconfig["uid_field"]."=". $uid);
        $ldap_user_info = ldap_get_entries($connect, $result);

         // ldap_get_entries returns true even if no results
         // are found, so check for number of rows in array
         if($ldap_user_info["count"]==0) return false;
         
         return $ldap_user_info;
    }
}

/**
 * search LDAP for user entities
 * @private
 * @author Richard Cave
 * @param args['connect'] open LDAP link connection
 * @param args['entry'] specific entry in LDAP directory
 * @param args['attribute'] attribute searching for (ie 'mail')
 * @returns int
 * @return attribute of entry, nothing otherwise
 */
function authldap__get_attribute_value($connect, $entry, $attribute)
{
    // what to do with more than one entry for user info?
    //$num_entries = ldap_count_entries($connect,$entry);

    // get attribute value
    $value = $entry[0][$attribute][0];
    return $value;
/*
    for ($i=0; $i<$num_entries; $i++) {  // loop though ldap search result
        error_log("user dn: " . $user_info[$i]["dn"]);
        for ($ii=0; $ii<$user_info[$i]["count"]; $ii++) {
            $attrib = $user_info[$i][$ii];
            eval("error_log( \$user_info[\$i][\"$attrib\"][0]);"); 
       }
*/
}

?>
