<?php

function xarbb_userapi_updatetopicsview($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($tid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('xarbb',
                          'user',
                          'gettopic',
                          array('tid' => $tid));

    if ($link == false) {
        $msg = xarML('No Such Topic Present',
                    'xarbb');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security Check
    if(!xarSecurityCheck('ReadxarBB')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];
    $time = date('Y-m-d G:i:s');
    $treplies = $link['treplies'] + 1;

    // Update the forum
    $query = "UPDATE $xbbtopicstable
            SET xar_ttime = '$time',
                xar_treplies = $treplies,
                xar_treplier = $treplier
            WHERE xar_tid = " . xarVarPrepForStore($tid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    return true;
}

?>