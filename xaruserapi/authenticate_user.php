<?php
/**
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/77102.html
 * @author Alexander GQ Gerasiov <gq@gq.pp.ru>
*/

/**
 * Login an externally authenticated user
 * @public
 * @param args['uname'] username of user
 * @param args['pass'] dummy password
 * @returns int
 * @return uid on successful login, XARUSER_AUTH_FAILED otherwise
 * @todo rework to make external user info access easily configurable, including LDAP
 */
function authphpbb2_userapi_authenticate_user($args)
{
    extract($args);

    if (!isset($uname) || !isset($pass) || $pass == "") {
        $msg = xarML('Empty uname (#(1)) or pass (not shown).', $uname);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }


    // open phpbb2 connection
    $connect = authphpbb2__open_phpbb2_connection();
    if (!$connect) return XARUSER_AUTH_FAILED;

    // get user information
    $user_info = authphpbb2__get_phpbb2_userdata($connect,$uname,$pass);
    if (!$user_info) {
        $msg = xarML('Wrong username (#(1)) or pass (not shown).', $uname);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }

    // OK, authentication worked
    // now we still have to fetch the $uid for return

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Get user information from roles
    $userRole = xarModAPIFunc('roles', 'user', 'get', array('uname' => $uname));


    if (!$userRole) {
        if (xarModGetVar('authphpbb2','add_user')!='true')
            return XARUSER_AUTH_FAILED;
            
        // add a user that does NOT exist in the database
        // get username from array
        $realname = $user_info['username'];
        // get email from array
        $email = $user_info['user_email'];

        // call role module to create new user role
        $now = time();
        $rid = xarModAPIFunc('roles', 'admin', 'create',
                             array('uname' => $uname, 
                                   'realname' => $realname, 
                                   'email' => $email, 
                                   'pass' => $pass,
                                   'date'     => $now,
                                   'valcode'  => 'createdbyphpbb2',
                                   'state'   => 3,
                                   'authmodule'  => 'authphpbb2'));

        if (!$rid)
            return XARUSER_AUTH_FAILED;

        $usergroup = xarModGetVar('authphpbb2','defaultgroup');

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
    
    // Create a session in phpBB2
    authphpbb2__board_login($connect, $uname, $pass);
    
    // close phpbb2 connection --may not be necessary
    authphpbb2__close_phpbb2_connection($connect);
    
    return $rid;
}

// PRIVATE FUNCTIONS
function authphpbb2__open_phpbb2_connection()
{
    
    $server = xarModGetVar('authphpbb2','server');
    $uname = xarModGetVar('authphpbb2','username');
    $pwd = xarModGetVar('authphpbb2','password');  
    $database = xarModGetVar('authphpbb2','database');
    $dbtype= xarModGetVar('authphpbb2','dbtype');
    
    $db = NewADOConnection($dbtype);
    if (!$db) {
        $msg = "phpBB2: Loading ADOdb driver '".$server."' has failed: " . $db->ErrorMsg();
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_CONNECTION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        error_log("phpBB2 Error: Loading ADOdb driver '".$server."' failed");
        return;
    }
    $db->Connect($server, $uname, $pwd, $database);

    if (!$db) {
        $msg = "phpBB2: Connection to $server has failed: " . $db->ErrorMsg();
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_CONNECTION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        error_log("phpBB2 Error: Connection to $server failed");
        return;
    }
    return $db;
}

function authphpbb2__board_login($connect,$username,$password)
{
    //Not realized yet.
    return true;
}


function authphpbb2__close_phpbb2_connection($connect)
{
    return true;
}

function authphpbb2__get_phpbb2_userdata($connect,$username,$pass)
{
    $prefix = xarModGetVar('authphpbb2','prefix');
    $password = md5($pass);
    $table = $prefix.'users';

    if($connect)  //just double-checking the connection.
    {
        // connect to the invision database and get the user data
        //$inv_db = mysql_select_db($database, $connect);
        $query = "SELECT * FROM " . $table . " WHERE username=? AND user_password=?";
        
        $connect->SetFetchMode(ADODB_FETCH_ASSOC);
        
        $bindvars = array($username, $password);
        $result =& $connect->Execute($query,$bindvars);
    
        if (!$result) {
            //error
            $msg = "phpBB2: Query to $table has failed: " . $connect->ErrorMsg();
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SQL_ERROR',
                new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
            error_log("phpBB2 Error: Query to $table failed");
            return false;
        } 
        if ($result->EOF)
        {
            //incorrect login
        $msg = xarML('Wrong username (#(1)) or pass (not shown).', $username);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
            $result->Close();
            return false;
        }
        else {
            //correct login.  return uid.
                if ($result->fields['user_active']=='0') 
                {
                    //user inactive
                    $msg = xarML('User #(1) not activated.', $username);
                    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
                    $result->Close();
                    return false;    
                }
                else
                {
                    $user_data=$result->fields;
                    $result->Close();
                    return $user_data;
                }
        }
    }
}

?>