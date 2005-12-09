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
    $wrap = xarModGetVar('xardplink', 'use_wrap');

    $user_data = array();

    if (!xarUserIsLoggedIn()) {
        xarResponseRedirect(xarModURL('rules', 'user', 'account'));
    }
    // roles_userapi_get??
    // We need to get the user password string from the database
    $uid = xarUserGetVar('uid');
    $user = xarModApiFunc('roles', 'user','get', array('uid' => $uid));

    // Distribute user data
    $user_data['login'] = $user['uname'];
    $user_data['passwd'] = $user['pass'];
    $user_data['name'] = $user['name'];
    $user_data['email'] = $user['email'];

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
            header("Location: index.php?module=window&page=$url");
        } else {
            //header("Location: modules.php?op=modload&name=xardplink&file=index&url=$url");
            header("Location: index.php?xardplink&file=index&url=$url");
        }
    }
    exit;
}

?>
