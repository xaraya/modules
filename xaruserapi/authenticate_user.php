<?php
/**
 * AuthURL UserAPI Authentication Function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthURL
 * @link http://xaraya.com/index.php/release/42241.html
 * @author Court Shrock <shrockc@inhs.org>
 */

/**
 * authenticate a user
 * @public
 * @author Court Shrock
 * @param args['uname'] user name of user
 * @param args['pass'] password of user
 * @returns int
 * @return uid on successful authentication, XARUSER_AUTH_FAILED otherwise
 */
function authurl_userapi_authenticate_user($args)
{
    # Extract args
    extract($args);

    # Empty username and/or password always fails
    if (!isset($uname) || !isset($pass) || $pass == "") {
        $msg = xarML('Empty uname (#(1)) or pass (not shown).', $uname);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }// if

    $config['add_user'] = xarModGetVar('authurl', 'add_user');
    $config['auth_url'] = xarModGetVar('authurl', 'auth_url');
    $config['debug_level'] = xarModGetVar('authurl', 'debug_level');

        if ($config['debug_level'] >= 2) xarLogMessage("authURL: authenticating against `{$config['auth_url']}`");

    # CURL will fetch the url with the credentials provided
    $c = curl_init($config['auth_url']);
    curl_setopt($c, CURLOPT_HEADER, 1);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_USERPWD, "$uname:$pass");
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($c, CURLOPT_TIMEOUT, 4);
    $result = curl_exec($c);
    curl_close($c);

    if (trim($result) == '') {
      $msg = xarML("The site is experiencing trouble authenticating users....please try back again in a few minutes");
      xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ERROR',
                     new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
      return XARUSER_AUTH_FAILED;

    } else {
      # split the html result by lines, then take the first line and split by a space
      list(,$code) = explode(' ', array_shift(explode("\r\n", $result)));

      if ($code != 200) {
        # should expect a 401 if failed, but we don't want to authorize if there is a server error
                if ($config['debug_level'] >= 1) xarLogMessage("authURL: URL returned $code -- failure");
        return XARUSER_AUTH_FAILED;
      }// if
            if ($config['debug_level'] >= 2) xarLogMessage("authURL: URL returned $code -- success");

    }// if

    # user has been verified, now we still have to fetch the $uid for return

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    # Get user information from roles
    $userRole = xarModAPIFunc('roles', 'user', 'get', array('uname' => $uname));

    if (!$userRole) {
        # add a user that does NOT exist in the database
        if ($config['add_user'] == 'true') {
            $realname = $uname;
            $email = $uname.'@';

            # call role module to create new user role
            $now = time();
            $rid = xarModAPIFunc('roles', 'admin', 'create',
                                 array('uname' => $uname,
                                       'realname' => $realname,
                                       'email' => $email,
                                       'pass' => $pass,
                                       'date'     => $now,
                                       'valcode'  => 'createdbyauthurl',
                                       'state'   => 3,
                                       'authmodule'  => 'authurl'));

            if (!$rid) {
                if ($config['debug_level'] >= 1) xarLogMessage("authURL: user creation failed for `$uname`");
                return XARUSER_AUTH_FAILED;
            }// if

            $usergroup = xarModGetVar('authurl','default_group');

            # Get the list of groups
            if (!$groupRoles = xarGetGroups()) {
                if ($config['debug_level'] >= 1) xarLogMessage('authURL: could not get user group list');
                return XARUSER_AUTH_FAILED;
            }// if

            $groupId = 0;
            while (list($key,$group) = each($groupRoles)) {
                if ($group['name'] == $usergroup) {
                    $groupId = $group['uid'];
                    break;
                }// if
            }// while

            if ($groupId == 0) {
                if ($config['debug_level'] >= 1) xarLogMessage("authURL: default group ($usergroup) doesn't exist");
                return XARUSER_AUTH_FAILED;
            }// if

            # Insert the user into the default users group
            if( !xarMakeRoleMemberByID($rid, $groupId)) {
                if ($config['debug_level'] >= 1) xarLogMessage('authURL: failed to add user to default group');
                return XARUSER_AUTH_FAILED;
            }// if

            if ($config['debug_level'] >= 2) xarLogMessage("authURL: added user `$uname`");

        } else {
            $rid = XARUSER_AUTH_FAILED;
            if ($config['debug_level'] >= 1) xarLogMessage("authURL: user ($uname) doesn't exist and can't add users");
        }// if
    } else {
        $rid = $userRole['uid'];
    }// if

    return $rid;
}

?>