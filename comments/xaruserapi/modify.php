<?php

/**
 * Modify a comment
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_modify($args) 
{

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

    $text .= "\n<p>\n";

    // Let's leave a link for the changelog module if it is hooked to track comments
    if (xarModIsHooked('changelog', 'comments', 0)){
        $url = xarModUrl('changelog', 'admin', 'showlog', array('modid' => '14', 'itemid' => $cid));
        $text .= '<a href="' . $url . '" title="' . xarML('See Changes') .'">';
    }

    $text .= xarML('[Modified by: #(1) (#(2)) on #(3)]', 
                   xarUserGetVar('name'),
                   xarUserGetVar('uname'),
                   $modified_date);

    if (xarModIsHooked('changelog', 'comments', 0)){
        $text .= '</a>';
    }

    $text .= "\n</p>\n";

    $sql =  "UPDATE $xartable[comments]
                SET xar_title    = '". xarVarPrepForStore($title) ."',
                    xar_text  = '". xarVarPrepForStore($text) ."',
                    xar_anonpost = '". (empty($postanon) ? 0 : 1) ."'
              WHERE xar_cid='$cid'";

    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return;
    }
    // Call update hooks for categories etc.
    $args['module'] = 'comments';
    $args['itemtype'] = 0;
    $args['itemid'] = $cid;
    xarModCallHooks('item', 'update', $cid, $args);

    return true;
}

?>
