<?php

/**
 * Authenticate a user against the Xaraya database, using their email address
 * and password.
 *
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
              WHERE xar_email = '" . xarVarPrepForStore($uname) . "'";
    $result =& $dbconn->Execute($query);
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
