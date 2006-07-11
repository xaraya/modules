<?php
/**
 * Authenticate a user against the Xaraya database, using their email address
 * and password.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authemail Module
 * @link http://xaraya.com/index.php/release/10513.html
 * @author Roger Keays <r.keays@ninthave.net>
 */

 /*
 * @public
 * @author Marco Canini modified by Roger Keays for authemail
 * @param args['uname'] email address of user
 * @param args['pass'] password of user
 * @returns int
 * @return uid on successful authentication, XARUSER_AUTH_FAILED otherwise
 */
function authemail_userapi_authenticate_user($args)
{
    extract($args);
    assert('!empty($uname) && isset($pass)');
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    // Get user information
    $rolestable = $xartable['roles'];
    $query = "SELECT xar_uid,
                     xar_pass
              FROM $rolestable
              WHERE xar_email = ?";
    $bindvars = array($uname);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    if ($result->EOF) {
        $result->Close();
        return XARUSER_AUTH_FAILED;
    }
    list($uid, $realpass) = $result->fields;
    $result->Close();
    // Confirm that passwords match
    if (!xarUserComparePasswords($pass, $realpass, $uname, 
            substr($realpass, 0, 2))) {
        return XARUSER_AUTH_FAILED;
    }
    return $uid;
}
?>