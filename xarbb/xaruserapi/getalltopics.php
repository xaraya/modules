<?php

/**
 * get all topics
 * @returns array
 * @return array of links, or false on failure
 */
function xarbb_userapi_getalltopics($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    if (!isset($fid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }



    $links = array();

    // Security Check
    if(!xarSecurityCheck('ViewxarBB',1,'Forum')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    // Get links
    $query = "SELECT xar_tid,
                     xar_fid,
                     xar_ttitle,
                     xar_tposter,
                     xar_ttime,
                     xar_treplies,
                     xar_treplier,
                     xar_tstatus
            FROM $xbbtopicstable
            WHERE xar_fid = $fid
            ORDER BY xar_ttime DESC";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    $topics = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($tid, $fid, $ttitle, $tposter, $ttime, $treplies, $treplier, $tstatus) = $result->fields;
        if (xarSecurityCheck('ViewxarBB', 0,'Forum',"$fid:All")) {
            $topics[] = array('tid'     => $tid,
                              'fid'     => $fid,
                              'ttitle'  => $ttitle,
                              'tposter' => $tposter,
                              'ttime'   => $ttime,
                              'treplies'=> $treplies,
                              'treplier'=> $treplier,
                              'tstatus' => $tstatus);
        }
    }

    $result->Close();

    return $topics;
}

?>