<?php

/**
 * get a specific link
 * @poaram $args['lid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function xarbb_userapi_gettopic($args)
{
    extract($args);

    if (!isset($tid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    // Get links
    $query = "SELECT xar_tid,
                     xar_fid,
                     xar_ttitle,
                     xar_tpost,
                     xar_tposter,
                     xar_ttime,
                     xar_treplies,
                     xar_tstatus
            FROM $xbbtopicstable
            WHERE xar_tid = " . xarVarPrepForStore($tid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($tid, $fid, $ttitle, $tpost, $tposter, $ttime, $treplies, $tstatus) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('ReadxarBB')) return;

    $topic = array('tid'     => $tid,
                   'fid'     => $fid,
                   'ttitle'  => $ttitle,
                   'tpost'   => $tpost,
                   'tposter' => $tposter,
                   'ttime'   => $ttime,
                   'treplies'=> $treplies,
                   'tstatus' => $tstatus);

    return $topic;
}

?>