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
    $useeditstamp=xarModGetVar('comments','editstamp');
    $adminid = xarModGetVar('roles','admin');


    $modified_date = xarLocaleFormatDate("%B %d, %Y %I:%M %p",time());

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();



    // Let's leave a link for the changelog module if it is hooked to track comments
    if (xarModIsHooked('changelog', 'comments', 0)){
        $url = xarModUrl('changelog', 'admin', 'showlog', array('modid' => '14', 'itemid' => $cid));
        $text .= "\n<p>\n";
        $text .= '<a href="' . $url . '" title="' . xarML('See Changes') .'">';
    }
    if  (($useeditstamp ==1 ) ||
                     (($useeditstamp == 2 ) && (xarUserGetVar('uid')<>$adminid))) {
    $text .= "\n<p>\n";
    $text .= xarML('[Modified by: #(1) (#(2)) on #(3)]',
                   xarUserGetVar('name'),
                   xarUserGetVar('uname'),
                   $modified_date);
    $text .= "\n</p>\n";
   }
    if (xarModIsHooked('changelog', 'comments', 0)){
        $text .= '</a>';
        $text .= "\n</p>\n";
    }



    $sql =  "UPDATE $xartable[comments]
                SET xar_title    = ?, 
                    xar_text     = ?,
                    xar_anonpost = ?
              WHERE xar_cid      = ?";
    $bpostanon = empty($postanon) ? 0 : 1;
    $bindvars = array($title, $text, $bpostanon, $cid);

    $result = &$dbconn->Execute($sql,$bindvars);

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
