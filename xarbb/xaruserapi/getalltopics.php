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
                    '', 'userapi', 'get', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }





     list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];
    $xbbforumstable = $xartable['xbbforums'];

    // Get link
    $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                   array('cids' => array(),
                                        'modid' => xarModGetIDFromName('xarbb')));
    // make only one query to speed up
    // Get links
    $query = "SELECT xar_tid,
                     $xbbtopicstable.xar_fid,
                     xar_ttitle,
                     xar_tpost,
                     xar_tposter,
                     xar_ttime,
                     xar_treplies,
                     xar_tstatus,
                     xar_treplier,
                   	 xar_fname,
                     xar_fdesc,
                     xar_ftopics,
                     xar_fposts,
                     xar_fposter,
                     xar_fpostid,
                     {$categoriesdef['cid']}
            FROM $xbbtopicstable LEFT JOIN $xbbforumstable ON $xbbtopicstable.xar_fid = $xbbforumstable.xar_fid
            LEFT JOIN {$categoriesdef['table']} ON {$categoriesdef['field']} = $xbbforumstable.xar_fid
            {$categoriesdef['more']}
            WHERE {$categoriesdef['where']} AND $xbbforumstable.xar_fid = " . xarVarPrepForStore($fid);

   /* // Get links
    $query = "SELECT xar_tid,
                     xar_fid,
                     xar_ttitle,
                     xar_tpost,
                     xar_tposter,
                     xar_ttime,
                     xar_treplies,
                     xar_tstatus
            FROM $xbbtopicstable
            WHERE xar_tid = " . xarVarPrepForStore($tid);   */
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $topics = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($tid, $fid, $ttitle, $tpost, $tposter, $ttime, $treplies, $tstatus,$treplier,
    	$fname, $fdesc, $ftopics, $fposts, $fposter, $fpostid,$catid) = $result->fields;

	    if (xarSecurityCheck('ReadxarBB',0,'Forum',"$catid:$fid"))	{
            $topics[] = array('tid'     => $tid,
                   'fid'     => $fid,
                   'ttitle'  => $ttitle,
                   'tpost'   => $tpost,
                   'tposter' => $tposter,
                   'ttime'   => $ttime,
                   'treplies'=> $treplies,
                   'tstatus' => $tstatus,
                   'treplier' => $treplier,
                   'fname'   => $fname,
                   'fdesc'   => $fdesc,
                   'ftopics' => $ftopics,
                   'fposts'  => $fposts,
                   'fposter' => $fposter,
                   'fpostid' => $fpostid,
                   'catid'   => $catid);
        }
    }

    $result->Close();

    return $topics;
}

?>
