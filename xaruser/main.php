<?php
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarDPLink Module
 * @link http://xaraya.com/index.php/release/591.html
 * @author xarDPLink Module Development Team
 */
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'syslog');

function xardplink_user_main()
{
    $url = trim(xarModGetVar('xardplink', 'url'));
    $window = xarModGetVar('xardplink', 'use_window');
    $wrap = xarModGetVar('xardplink', 'use_postwrap');

    $user_data = array();
    $home = pnGetBaseURL();
  $home .= "user.php?op=loginscreen&module=NS-User";
    if (!xarUserLoggedIn()) {
        pnRedirect($home);
    }
    // We need to get the user password string from the database
    $uid = xarUserGetVar('uid');
    list($dbconn) = xarDBGetConn();
    $pntables = xarDBGetTables();
    $usertable = $xartables['users'];
    $usercol =& $xartables['users_column'];
    $sql = "SELECT $usercol[uname],
      $usercol[pass],
        $usercol[name],
        $usercol[email]
      FROM $usertable
        WHERE $usercol[uid] = $uid";
    $result = $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0)
        die("Could not get user details");
    if ($result->EOF)
        die("Could not get user detail");
    list($uname, $password, $user_name, $user_email) = $result->fields;
    $result->Close();
    $user_data['login'] = $uname;
    $user_data['passwd'] = $password;
    $user_data['name'] = $user_name;
    $user_data['email'] = $user_email;
    $parm = serialize($user_data);
    $check = md5($parm);
    $cparm = gzcompress($parm);
    $bparm = urlencode(base64_encode($cparm));
    if ( $window ) {
        $url .= "/index.php?login=pn&userdata=$bparm&check=$check";
        header("Location: $url");
    } else {
        $url .= "/index.php?login=pn%26userdata=$bparm%26check=$check";
        if ($wrap) {
            header("Location: modules.php?op=modload&name=PostWrap&file=index&page=$url");
        } else {
            header("Location: modules.php?op=modload&name=xardplink&file=index&url=$url");
        }
    }
    exit;
}

?>
