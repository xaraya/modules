<?php
/**
 * AuthSQL User API
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSQL Module
 * @link http://xaraya.com/index.php/release/10512.html
 * @author Roger Keays and James Cooper
*/

/**
 * authenticate a user
 * @public
 * @author James Cooper
 * @param args['uname'] user name of user
 * @param args['pass'] password of user
 * @returns int
 * @return uid on successful authentication, XARUSER_AUTH_FAILED otherwise
 */
function authsql_userapi_authenticate_user($args)
{
    // Extract args
    extract($args);

    if (!isset($uname) || !isset($pass) || $pass == "") {
        $msg = xarML('Empty uname (#(1)) or pass (not shown).', $uname);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }

    // Get module variables and set in array
    $sqlconfig = array();

    $sqlconfig['sqldbhost'] = xarModGetVar('authsql', 'sqldbhost');
    $sqlconfig['sqldbport'] = xarModGetVar('authsql', 'sqldbport');
    $sqlconfig['sqldbtype'] = xarModGetVar('authsql', 'sqldbtype');
    $sqlconfig['sqldbname'] = xarModGetVar('authsql', 'sqldbname');
    $sqlconfig['sqldbuser'] = xarModGetVar('authsql', 'sqldbuser');
    $sqlconfig['sqldbpass'] = xarModGetVar('authsql', 'sqldbpass');
    $sqlconfig['sqlwhere'] = xarModGetVar('authsql', 'sqlwhere');
    $sqlconfig['sqldbpasswordtablename'] = 
        xarModGetVar('authsql', 'sqldbpasswordtablename');
    $sqlconfig['sqldbusernamefield'] = 
        xarModGetVar('authsql', 'sqldbusernamefield');
    $sqlconfig['sqldbpasswordfield'] = 
        xarModGetVar('authsql', 'sqldbpasswordfield');
    $sqlconfig['sqldbpasswordencryptionmethod'] = 
        xarModGetVar('authsql', 'sqldbpasswordencryptionmethod');

    $sqlconfig['add_user'] = xarModGetVar('authsql','add_user');
    $sqlconfig['store_user_password'] = 
        xarModGetVar('authsql','store_user_password');

    // Create new connection, ensure it exists and show debug info
    $dbconn = ADONewConnection($sqlconfig['sqldbtype']);
    $dbconn->debug = false;

    // only connect using port if a port is given
    if (!isset($sqlconfig['sqldbport']) || $sqlconfig['sqldbport'] == "") {
        $dbconn->Connect($sqlconfig['sqldbhost'], 
                      $sqlconfig['sqldbuser'], 
                      $sqlconfig['sqldbpass'], 
                      $sqlconfig['sqldbname'],
                      true);
    } else {
        $dbconn->Connect($sqlconfig['sqldbhost'].':'.$sqlconfig['sqldbport'], 
                      $sqlconfig['sqldbuser'], 
                      $sqlconfig['sqldbpass'], 
                      $sqlconfig['sqldbname'],
                      true);
    }

    if ($dbconn === false) {
        $msg = __FILE__.'('.__LINE__.'): '.
            xarML("Couldn't connect to #(1):#(2) #(3)@#(4) #5",
            $sqlconfig['sqldbhost'],
            $sqlconfig['sqldbport'], 
            $sqlconfig['sqldbuser'], 
            $sqlconfig['sqldbpass'], 
            $sqlconfig['sqldbname']);
        xarLogMessage($msg, XARLOG_LEVEL_INFO);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException($msg));
        return; 
    }

    // successfully connected - whew!
    $msg = __FILE__.'('.__LINE__.'): '.
        xarML("Connected to #(1):#(2) #(3) #4",
        $sqlconfig['sqldbhost'],
        $sqlconfig['sqldbport'], 
        $sqlconfig['sqldbuser'], 
        $sqlconfig['sqldbname']);
    xarLogMessage($msg, XARLOG_LEVEL_INFO);

    // now check password
    $sqlQuery =
        'SELECT '.$sqlconfig['sqldbpasswordfield'].' '.
        'FROM '.$sqlconfig['sqldbpasswordtablename'].' '.
        'WHERE '.
        $sqlconfig['sqldbusernamefield'].'=?';

    /* add custom where field */
    if (!empty($sqlconfig['sqlwhere'])) {
        $sqlQuery .= ' AND '.$sqlconfig['sqlwhere'];
    }
    $rs = $dbconn->GetOne($sqlQuery, array($uname));
    xarLogMessage(xarML("Password is #(1)", $rs));
    if ($rs === false) {   // failed to get password
        $msg = __FILE__.'('.__LINE__.'): '.
            xarML("After connection to #(1):#(2) (#(3)@#(4)) #(5) ".
            "the following SQL query did not return a RecordSet: #(6)",
            $sqlconfig['sqldbhost'],
            $sqlconfig['sqldbport'], 
            $sqlconfig['sqldbuser'], 
            $sqlconfig['sqldbpass'], 
            $sqlconfig['sqldbname'],
            $sqlQuery);
        xarLogMessage($msg, XARLOG_LEVEL_DEBUG);
        return XARUSER_AUTH_FAILED;
    } else {
// ??? need other enc methods also
        if ($sqlconfig['sqldbpasswordencryptionmethod'] == 'md5') {
            $encryptedpassword = md5($pass);
        } else if ($sqlconfig['sqldbpasswordencryptionmethod'] == 'crypt') {
            $encryptedpassword = crypt($pass, $rs);
        } else if ($sqlconfig['sqldbpasswordencryptionmethod'] == 'encrypt') {
            $encryptedpassword = encrypt($pass);
        } else if ($sqlconfig['sqldbpasswordencryptionmethod'] == 'decrypt') {
            $encryptedpassword = decrypt($pass);
        } else {   // plaintext
            $encryptedpassword = $pass;
        }
        if (strcmp($rs, $encryptedpassword) != 0) {
            $msg = __FILE__.'('.__LINE__.'): '.
                xarML("After connection to #(1):#(2) (#(3)@#(4)) #(5)".
                "using query: #(6) ".
                "the encrypted password did not match: ".
                "password=#(7) encryptedpassword=#(8) database value=#(9). ",
                $sqlconfig['sqldbhost'],
                $sqlconfig['sqldbport'], 
                $sqlconfig['sqldbuser'], 
                $sqlconfig['sqldbpass'], 
                $sqlconfig['sqldbname'],
                $sqlQuery, 
                '*', // $pass - don't log the password
                $encryptedpassword, 
                $rs);
            xarLogMessage($msg, XARLOG_LEVEL_DEBUG);
            return XARUSER_AUTH_FAILED;
        }
    } /* if $rs === false */

    $dbconn->Close();

    // OK, authentication worked
    // now we still have to fetch the $uid for return

    // Get user information from roles
    $userRole = xarModAPIFunc('roles', 'user', 'get',
        array('uname' => $uname));

    if (!$userRole) {
        // add a user that does NOT exist in the database
        if ($sqlconfig['add_user'] == 'true') {
            $realname = "";
            $email = "";

            // Check if we're going to store the user password
            if ($sqlconfig['store_user_password'] == 'true') {
                $password = $pass;
            } else {
                // Create a dummy password
                $password = xarModAPIFunc('roles', 'user', 'makepass');
            }

            // call role module to create new user role
            $now = time();
            $rid = xarModAPIFunc('roles', 'admin', 'create',
                array('uname' => $uname, 
                'realname' => $realname, 
                'email' => $email, 
                'pass' => $password,
                'date'     => $now,
                'valcode'  => 'createdbysql',
                'state'   => 3,
                'authmodule'  => 'authsql'));

            if (!$rid) {
                $msg = __FILE__.'('.__LINE__.'): '.
                    'Unable to create new user role with '.
                    'uname='.$uname.' '.
                    'realname='.$realname.' '. 
                    'email='.$email.' '. 
                    'pass='.'*'.' '. // $password - don't log the password
                    'date='.$now.' '.
                    'valcode=createdbysql '.
                    'state=3 '.
                    'authmodule=authsql';
                xarLogMessage($msg, XARLOG_LEVEL_DEBUG);
                return XARUSER_AUTH_FAILED;
            }

            $usergroup = xarModGetVar('authsql','defaultgroup');

            // Get the role for this group
            $role = xarFindRole($usergroup);

            if (!isset($role)) {
                $msg = __FILE__.'('.__LINE__.'): '.
                    'Unable to get the role with '.
                    'usergroup='.$usergroup;
                xarLogMessage($msg, XARLOG_LEVEL_DEBUG);
                return XARUSER_AUTH_FAILED;
            }

            // Insert the user into the default users group
            if (!xarMakeRoleMemberByID($rid, $role->getID())) {
                $msg = __FILE__.'('.__LINE__.'): '.
                    'Unable to insert the user into '.
                    'the default users group with '.
                    '$rid='.$rid.' '.
                    '$role='.$role;
                xarLogMessage($msg, XARLOG_LEVEL_DEBUG);
                return XARUSER_AUTH_FAILED; 
            }
        } else {
            $rid = XARUSER_AUTH_FAILED;
        }
    } else {
        $rid = $userRole['uid'];

        // Check if we need to synchronize passwords
        if ($sqlconfig['store_user_password']) {

            // Compare new and old password
            $md5password = xarUserComparePasswords($pass, $userRole['pass'], 
                $userRole['uname']);

            if (!$md5password) {
                // Update Xaraya database with new password
                $res = xarModAPIFunc('roles', 'admin', 'update',
                    array('uid' => $userRole['uid'],  
                    'name' => $userRole['name'], 
                    'uname' => $userRole['uname'], 
                    'email' => $userRole['email'], 
                    'state' => $userRole['state'], 
                    'valcode' => $userRole['valcode'], 
                    'pass' => $pass));
                if (!$res) {
                    $msg = __FILE__.'('.__LINE__.'): '.
                       'Unable to update Xaraya database with new password. '.
                       '$res is '.$res;
                    xarLogMessage($msg, XARLOG_LEVEL_DEBUG);
                    return XARUSER_AUTH_FAILED;
                }
            }
        }
    }

    return $rid;
}
?>