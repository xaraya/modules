<?php
function authinvision2_eventapi_onUserLogin($userId)
{
    //-------------------------------------
    // Invision Board database information
    //-------------------------------------
    $server = xarModGetVar('authinvision2','server');
    $username = xarModGetVar('authinvision2','username');
    $pwd = xarModGetVar('authinvision2','password');
    $prefix = xarModGetVar('authinvision2','prefix');
    $db = xarModGetVar('authinvision2','database');
    $connect = mysql_connect($server, $username, $pwd);
    $userRole = xarModAPIFunc('roles', 'user', 'get', array('uid' => $userId));

    if (empty($userRole)) {
        $msg = xarML('No role defined');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);
        return;
    }
    if (!$connect || !mysql_select_db($db,$connect)) {
        $msg = xarML('DB Error: connection or database selection failed');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SQL_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);
        return;
    }

    xarVarFetch('invisibleme','checkbox',$invisibleme,0,
                 XARVAR_DONT_REUSE+XARVAR_NOT_REQUIRED);
    if (!$invisibleme) {
        $invisibleme = 0;
        $login_anon = '0&1';
    } else {
        $login_anon = '1&1';
    }

    //---------------------------------------
    // Retrieve the cookie settings from IPB
    //---------------------------------------
    $sql = "SELECT conf_value FROM ".$prefix."_conf_settings where conf_key='cookie_id' or conf_key='cookie_path' or conf_key='cookie_domain'";
    $result = mysql_query($sql,$connect);
    $i = 0;
    //------------------------------------
    // Indexes: [0]domain, [1]id, [2]path
    //------------------------------------
    while ($row = mysql_fetch_assoc($result)) {
        $ipb_conf[$i] = $row['conf_value'];
        $i++;
    }
    $sql = "SELECT * FROM ".$prefix."_members WHERE name='".$userRole['uname']."'";
    $result = mysql_query($sql,$connect);
    if (!$result) {
        $msg = xarML('DB error: query failed');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SQL_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);
        return;
    } else {
        $user_info = mysql_fetch_assoc($result);
    }
    if (isset($_COOKIE[$ipb_conf[1]."session_id"])) {
        //----------------------------
        // Get session_id from cookie
        //----------------------------
        $ipb_session_id = $_COOKIE[$ipb_conf[1]."session_id"];
        $sql = "UPDATE ".$prefix."_sessions SET member_name='".$user_info['name']."', member_id='".$user_info['id']."', login_type='".$invisibleme."' WHERE id='".$ipb_session_id."'";
    } else {
        //---------------------------------------
        // No cookie, so create a new session ID
        //---------------------------------------
        $ipb_session_id = md5(uniqid(microtime()));
        $curTime = time();
        $sql = "INSERT into ".$prefix."_sessions VALUES('".$ipb_session_id."','".$user_info['name']."','".$user_info['id']."','".$_SERVER['REMOTE_ADDR']."','".substr($_SERVER['HTTP_USER_AGENT'],0,50)."','".$curTime."','".$invisibleme."','','".$user_info['mgroup']."','','','')";
    }
    $result = mysql_query($sql,$connect);
    if (!$result) {
        //-----------------------------------
        // Exit as the session update failed
        //-----------------------------------
        $msg = xarML('DB Error: query failed');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SQL_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);
        return;
    }
    $sql = "UPDATE ".$prefix."_members SET login_anonymous='".$login_anon."' WHERE name='".$user_info['name']."'";
    $result = mysql_query($sql,$connect);

    //-----------------
    // Set the cookies
    //-----------------
    $end = time() + 31536000;
    setcookie($ipb_conf[1]."member_id",$user_info['id'],$end,$ipb_conf[2],$ipb_conf[0]);
    setcookie($ipb_conf[1]."pass_hash",$user_info['member_login_key'],$end,$ipb_conf[2],$ipb_conf[0]);
    setcookie($ipb_conf[1]."session_id",$ipb_session_id,$end,$ipb_conf[2],$ipb_conf[0]);

    // This is needed "just in case" the connection to the Invision database
    // uses the same parameters as that used by Xaraya (i.e. same hostname
    // and username)
    mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);

    return true;
}

function authinvision2_eventapi_OnUserLogout($userID)
{
    //-------------------------------------
    // Invision Board database information
    //-------------------------------------
    $server = xarModGetVar('authinvision2','server');
    $username = xarModGetVar('authinvision2','username');
    $pwd = xarModGetVar('authinvision2','password');
    $prefix = xarModGetVar('authinvision2','prefix');
    $db = xarModGetVar('authinvision2','database');

    $connect = mysql_connect($server, $username, $pwd);
    if (!$connect || !mysql_select_db($db,$connect)) {
        $msg = xarML('DB Error: connection or database selection failed.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SQL_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);
        return;
    }

    //---------------------------------------
    // Retrieve the cookie settings from IPB
    //---------------------------------------
    $sql = "SELECT conf_value FROM ".$prefix."_conf_settings where conf_key='cookie_id' or conf_key='cookie_path' or conf_key='cookie_domain'";
    $result = mysql_query($sql,$connect);
    $i = 0;
    //------------------------------------
    // Indexes: [0]domain, [1]id, [2]path
    //------------------------------------
    while ($row = mysql_fetch_assoc($result)) {
        $ipb_conf[$i] = $row['conf_value'];
        $i++;
    }
    if (isset($_COOKIE[$ipb_conf[1]."session_id"])) {
        //----------------------------
        // Get session_id from cookie
        //----------------------------
        $ipb_session_id = $_COOKIE[$ipb_conf[1]."session_id"];
        $sql = "UPDATE ".$prefix."_sessions set member_name='',member_id='', login_type='' WHERE id='".$ipb_session_id."'";
        mysql_query($sql,$connect);
        $end = time() + 31536000;
        setcookie($ipb_conf[1]."member_id","0",$end,$ipb_conf[2],$ipb_conf[0]);
        setcookie($ipb_conf[1]."pass_hash","0",$end,$ipb_conf[2],$ipb_conf[0]);
    } // If the cookie isn't set, we aren't worried about logging the user out

    // This is needed "just in case" the connection to the Invision database
    // uses the same parameters as that used by Xaraya (i.e. same hostname
    // and username)
    mysql_select_db($GLOBALS['xarDB_systemArgs']['databaseName']);

    return true;
}
?>
