<?php
/**
 * authinvision User API
 * 
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authinvision
 * @link http://xaraya.com/index.php/release/950.html
 * @author ladyofdragons
 */

//$GLOBALS['xarDB_systemArgs']['databaseName']

/**
 * check whether this module has a certain capability
 * @public
 * @param args['capability'] the capability to check for
 * @author Marco Canini
 * @returns bool
 */
function authinvision_userapi_has_capability($args)
{
    extract($args);

    if (!isset($capability)) {
        $msg = xarML('Empty capability.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
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
    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
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
function authinvision_userapi_authenticate_user($args)
{
    extract($args);

    if (!isset($uname) || !isset($pass) || $pass == "") {
        $msg = xarML('Empty uname (#(1)) or pass (not shown).', $uname);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }


    // open invision connection
    $connect = authinvision__open_invision_connection();
    if (!$connect) return XARUSER_AUTH_FAILED;

    // get user information
    $user_info = authinvision__get_invision_userdata($connect,$uname,$pass);
    if (!$user_info) return XARUSER_AUTH_FAILED;
    // OK, authentication worked
    // now we still have to fetch the $uid for return

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Get user information from roles
    $userRole = xarModAPIFunc('roles', 'user', 'get',
                              array('uname' => $uname)); 

    if (!$userRole) {
        // add a user that does NOT exist in the database
            $realname = "";
            $email = "";
            // get username from array
            $realname = authinvision__get_attribute_value($connect,$user_info,'name');
            // get email from array
            $email = authinvision__get_attribute_value($connect,$user_info,'email');

            // call role module to create new user role
            $now = time();
            $rid = xarModAPIFunc('roles', 'admin', 'create',
                                 array('uname' => $uname, 
                                       'realname' => $realname, 
                                       'email' => $email, 
                                       'pass' => $pass,
                                       'date'     => $now,
                                       'valcode'  => 'createdbyinvision',
                                       'state'   => 3,
                                       'authmodule'  => 'authinvision'));

            if (!$rid)
                return XARUSER_AUTH_FAILED;

            $usergroup = xarModGetVar('authinvision','defaultgroup');

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
        $rid = $userRole['uid'];
    }

    // retrieve a session ID for invision
    //$boardlogin = authinvision__board_login($connect, $uname, $pass);
    
    // close invision connection --may not be necessary
    //authinvision__close_invision_connection($connect);

    return $rid;
}

/**
 * check whether a user variable is avaiable from this module (currently unused)
 * @public
 * @author Marco Canini
 * @returns boolean
 */
function authinvision_userapi_is_valid_variable($args)
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
function authinvision_userapi_get_user_variable($args)
{
    // Second level cache
    static $vars = array();

    extract($args);

    if (!isset($uid) || !isset($name)) {
        $msg = xarML('Empty uid (#(1)) or name (#(2))', $uid, $name);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
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
        //    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
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
function authinvision_userapi_set_user_variable($args)
{
    extract($args);

    if (!isset($uid) || !isset($name) || !isset($value)) {
        $msg = xarML('Empty uid (#(1)) or name (#(2)) or value (#(3)).', $uid, $name, $value);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // ...update the user variable in the external auth system if applicable...

    // throw back an exception if the user doesn't exist
    //if (...) {
    //    $msg = xarML('User identified by uid #(1) doesn\'t exist.', $uid);
    //    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
    //                  new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    //    return;
    //}

    return true;
}

function authinvision_userapi_getlast()
{
    $connect = authinvision__open_invision_connection();
    if (!$connect) return XARUSER_AUTH_FAILED;
    
    if($connect)  //just double-checking the connection.
    {
        // connect to the invision database and get the user data
        
        $prefix = xarModGetVar('authinvision','prefix');
        $database = xarModGetVar('authinvision','database');
        $table = $prefix.'_members';
        
        $sql = "SELECT name FROM $database.$table ORDER BY joined DESC LIMIT 1";
        $result = mysql_query($sql,$connect);

        if (!$result) {
            //incorrect login.
            return false;
        } else {
            //correct login.  return name
            while ($row = mysql_fetch_array($result)) {
                $lastuser = $row;
            }
        }
    }
    
    //reset us back to the xaraya database.    
    $xardb = $GLOBALS['xarDB_systemArgs']['databaseName'];
    mysql_select_db($xardb);
    
    return $lastuser;
}

/* get the number of new private messages from invision */
function authinvision_userapi_getmessages($args)
{
   extract($args);
   
   $prefix = xarModGetVar('authinvision','prefix');
   $database = xarModGetVar('authinvision','database');
   $table = $prefix.'_messages';
   
    $connect = authinvision__open_invision_connection();
    if (!$connect) return XARUSER_AUTH_FAILED;
    
    if($connect)  //just double-checking the connection.
    {
   
       $user_info = authinvision__get_invision_publicuserdata($username);
       if (!$user_info) {
          //no matching user.  let's just say there are no messages.
          return 0;
       }
       $inv_id = $user_info['id'];
       $sql = "SELECT COUNT(*) as msg_total FROM $database.$table WHERE recipient_id='$inv_id' AND vid = 'in' AND read_date is null";
       $result = mysql_query($sql,$connect);
       
       if (!$result) {
       //an error occurred.  let's just say there are no messages.
       return 0;
       } else {
          //we've got a record.  find out how many.
           while ($row = mysql_fetch_array($result)) {
            $invision_messagecount = $row['msg_total'];
           }
       }
           //reset us back to the xaraya database.
        $xardb = $GLOBALS['xarDB_systemArgs']['databaseName'];
        mysql_select_db($xardb);
        return $invision_messagecount;
    }
}
/*
function authinvision_userapi_get_all_users($args)
{

}
*/

/*
function authinvision_userapi_get_authorization_info($args)
{

}
*/

/*
function authinvision_userapi_create_user($args)
{

}
*/

/*
function authinvision_userapi_delete_user($args)
{

}
*/

// PRIVATE FUNCTIONS

/**
 * open ldap connection
 * @private
 * @author Richard Cave
 * @returns int
 * @return LDAP link identifier on connect, false otherwise
 */
function authinvision__open_invision_connection()
{
    
    $server = xarModGetVar('authinvision','server');
    $uname = xarModGetVar('authinvision','username');
    $pwd = xarModGetVar('authinvision','password');  

// TODO: use xarDBNewConn() and ADODB methods everywhere in this module

    $connect = @mysql_connect($server, $uname, $pwd);

    if (!$connect) {
        $msg = "Invision: Connection to $server has failed: " . mysql_error();
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_CONNECTION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        error_log("Invision Error: Connection to $server failed");
        return;
    }
    return $connect;
}

/**
 * log on to the actual board so you don't have to log on there if you surf there.
 * @private
 * @author Marie Altobelli
 * @param args['connect'] open connection.
 * @returns int
 * @return true on success, false otherwise
 */
function authinvision__board_login($connect,$username,$password)
{
    $path = xarModGetVar('authinvision','forumroot');
    require $path."/mod_login_to_ipb.php";
    $validated = validate($username, $password, '', $path);
    
    //reset us back to the xaraya database.    
    $xardb = $GLOBALS['xarDB_systemArgs']['databaseName'];
    mysql_select_db($xardb);
    
    return $validated;
}

/**
 * log on to the actual board so you don't have to log on there if you surf there.
 * @private
 * @author Marie Altobelli
 * @param args['connect'] open connection.
 * @returns int
 * @return true on success, false otherwise
 */
function authinvision__board_logout($connect,$username,$password)
{
    $path = xarModGetVar('authinvision','forumroot');
    
    require $path."/mod_logout_from_ipb.php";
    
    //reset us back to the xaraya database.
    $xardb = $GLOBALS['xarDB_systemArgs']['databaseName'];
    mysql_select_db($xardb);
    return true;
}

/**
 * close ldap connection
 * @private
 * @author Richard Cave
 * @param args['connect'] open LDAP link connection
 * @returns int
 * @return true on success, false otherwise
 */
function authinvision__close_invision_connection($connect)
{

    return true;
}

/**
 * fetch invision userdata
 * @private
 * @author Marie Altobelli
 * @param args['connect'] open invision DB connection
 * @param args['uname'] user name of user
 * @param args['pass'] password of user
 * @returns int
 * @return uid on successful authentication, XARUSER_AUTH_FAILED otherwise
 */
function authinvision__get_invision_userdata($connect,$username,$pass)
{
    $server = xarModGetVar('authinvision','server');
    $prefix = xarModGetVar('authinvision','prefix');
    $database = xarModGetVar('authinvision','database');
    $version = xarModGetVar('authinvision','version');
    $password = md5($pass);
    $table = $prefix.'_members';
    $table2 = $prefix.'_members_converge';

    if($connect)  //just double-checking the connection.
    {
        // connect to the invision database and get the user data
        //$inv_db = mysql_select_db($database, $connect);
        if (empty($version) || $version == '1') {
            $sql = "SELECT * FROM $database.$table WHERE username=? AND user_password=?";
            $bindvars = array($username, $password);
            $result = mysql_query($sql,$connect,$bindvars);
        } elseif ($version == '2') {
            // cfr. converge_authenticate_member() method in ips_kernel/class_converge.php
            $sql = "SELECT *
                    FROM $database.$table
                    LEFT JOIN $database.$table2
                           ON email = converge_email
                    WHERE name= ?
                      AND converge_pass_hash = MD5(CONCAT(MD5(converge_pass_salt),?))";
            $bindvars = array($username, $password);
            $result = mysql_query($sql,$connect,$bindvars);
        }
    
        if (!$result) {
        //incorrect login.
            $msg = "Invision: Query to $table has failed: " . mysql_error();
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SQL_ERROR',
                new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
            error_log("Invision Error: Query to $table failed");
            return false;
        } else {
            //correct login.  return uid.
            while ($row = mysql_fetch_array($result)) {
                $invision_user_info = $row;
            return $invision_user_info;
            }
        }
    }
}

function authinvision__get_invision_publicuserdata($username)
{
    $server = xarModGetVar('authinvision','server');
    $prefix = xarModGetVar('authinvision','prefix');
    $database = xarModGetVar('authinvision','database');
    $table = $prefix.'_members';
    
    // open invision connection
    $connect = authinvision__open_invision_connection();
    if (!$connect) return XARUSER_AUTH_FAILED;

    if($connect)  //just double-checking the connection.
    {
        // connect to the invision database and get the user data
          //$inv_db = mysql_select_db($database, $connect);
          $sql = "SELECT id, name, mgroup, email, joined, avatar, posts, aim_name, icq_number, location, signature, website, yahoo, title, time_offset, interests, hide_email FROM $database.$table WHERE name='$username'";
          $result = mysql_query($sql,$connect);
  
          if (!$result || mysql_num_rows($result)==0) {
              //incorrect login.
              return false;
          } else {
              //correct login.  return userdata.
              while ($row = mysql_fetch_array($result)) {
                  $invision_user_info = $row;
              }
      }
    }
    
    //reset us back to the xaraya database.    
    $xardb = $GLOBALS['xarDB_systemArgs']['databaseName'];
    mysql_select_db($xardb);
    //why on earth I need this I'm not sure, but it's here to fix bug #1264.
    if (!$invision_user_info) { return false; }  
    return $invision_user_info;
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
function authinvision__get_attribute_value($connect, $entry, $attribute)
{

    // get attribute value
    $value = $entry[$attribute];
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

/**
 * utility function pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function authinvision_userapi_getmenulinks()
{
    // No menu links for users
    $menulinks = '';
    return $menulinks;
}

?>
