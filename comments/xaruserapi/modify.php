<?php

/**
 * Modify a comment
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_modify($args) {

    extract($args);

    $msg = xarML('Missing or Invalid Parameters: ');;
    $error = FALSE;

    if (!isset($title)) {
        $msg .= xarMLbykey('title ');
        $error = TRUE;
    }

    if (!isset($cid)) {
        $msg .= xarMLbykey('cid ');
        $error = TRUE;
    }

    if (!isset($text)) {
        $msg .= xarMLbykey('text ');
        $error = TRUE;
    }

    if (!isset($postanon)) {
        $msg .= xarMLbykey('postanon ');
        $error = TRUE;
    }

    if ($error) {
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $hostname = preg_replace('/,.*/', '', $forwarded);
    } else {
        $hostname = xarServerGetVar('REMOTE_ADDR');
    }

    $modified_date = xarLocaleFormatDate("%B %d, %Y %I:%M %p",time());

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $text .= "\n<br />\n<br />\n";
    $text .= xarML('[Modified by: #(1) (#(2)) on #(3)]', 
                   xarUserGetVar('name'),
                   xarUserGetVar('uname'),
                   $modified_date);

    $sql =  "UPDATE $xartable[comments]
                SET xar_title    = '". xarVarPrepForStore($title) ."',
                    xar_text  = '". xarVarPrepForStore($text) ."',
                    xar_anonpost = '". (empty($postanon) ? 0 : 1) ."'
              WHERE xar_cid='$cid'";

    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return;
    }

}

?>
