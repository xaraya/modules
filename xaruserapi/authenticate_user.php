<?php
/**
 * File: $Id$
 *
 * Authenticate a user
 *
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage authinvision2
 * @author Brian McCloskey
*/
/**
 * authenticate a user
 * @public
 * @author Brian McCloskey
 * @param args['uname'] user name of user
 * @param args['pass'] password of user
 * @returns int
 * @return uid on successful authentication, XARUSER_AUTH_FAILED otherwise
 */
function authinvision2_userapi_authenticate_user($args)
{
    extract($args);

    assert('!empty($uname) && isset($pass)');

    //-------------------------------------
    // Invision Board database information
    //-------------------------------------
    $server = xarModGetVar('authinvision2','server');
    $username = xarModGetVar('authinvision2','username');
    $pwd = xarModGetVar('authinvision2','password');
    $prefix = xarModGetVar('authinvision2','prefix');
    $db = xarModGetVar('authinvision2','database');
    $forumRoot = xarModGetVar('authinvision2','forumroot');

    //---------------------------------------------
    // IPB cleans the password with the FUNC class
    //---------------------------------------------
    include $forumRoot."/sources/functions.php";
    $std = new FUNC();
    $passInvision = $std->clean_value($pass);


    //---------------------------
    // Open Xaraya DB connection
    //---------------------------
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    //-----------------------------
    // Open Invision DB connection
    //-----------------------------
    $connect = mysql_connect($server, $username, $pwd);
    if (!$connect || !mysql_select_db($db,$connect)) {
        db_switch();
        return XARUSER_AUTH_FAILED;
    }

    //-------------------------------------
    // Check for user in Invision Board DB
    //-------------------------------------
    $sql = "SELECT m.* FROM ".$prefix."_members m INNER JOIN ".$prefix."_members_converge c on c.converge_id=m.id where m.name='".$uname."' and md5(CONCAT(md5(c.converge_pass_salt), md5('".$passInvision."'))) = c.converge_pass_hash";
    $result = mysql_query($sql,$connect);

    if (!$result || !mysql_fetch_array($result)) {
        db_switch();
        return XARUSER_AUTH_FAILED;
    } else {
        $sql = "SELECT * FROM ".$prefix."_members WHERE name='".$uname."'";
        $result = mysql_query($sql,$connect);
        if (!$result) {
            $msg = xarML('DB Error: query failed');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SQL_ERROR',
                           new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
            db_switch();
            return;
        } else {
            $user_info = mysql_fetch_assoc($result);
        }

        db_switch();
        //--------------------------------------
        // Check if user exists in Xaraya roles
        //--------------------------------------
        $xarUser = xarModAPIFunc('roles','user','get',array('uname' => $uname));
        if (!$xarUser) {
            $rid = xarModAPIFunc('roles','admin','create',
                array('uname' => $uname,
                      'realname' => $user_info['name'],
                      'email' => $user_info['email'],
                      'pass' => $pass,
                      'date' => $user_info['joined'],
                      'valcode' => 'createdbyinvision2',
                      'state' => 3,
                      'authmodule' => 'authinvision2'));
            if (!$rid) {
                error_log("user not created");
                mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);
                return XARUSER_AUTH_FAILED;
            }
            $usergroup = xarModGetVar('authinvision2','defaultgroup');

            if (!$groupRoles = xarGetGroups()) return;
            while (list($key,$group) = each($groupRoles)) {
                if ($group['name'] == $usergroup) {
                    $groupId = $group['uid'];
                    break;
                }
            }
            if ($groupId == 0) return;

            if (!xarMakeRoleMemberByID($rid, $groupId)) {
                mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);
                return XARUSER_AUTH_FAILED;
            }
        } elseif ($xarUser['state'] == ROLES_STATE_INACTIVE) {
            $msg = xarML('Your account has been marked as inactive.  Contact the administrator with further questions.');
            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        } else {
            $rid = $xarUser['uid'];
        }
    }
    return $rid;
}

function db_switch() 
{
    mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);
}
?>
