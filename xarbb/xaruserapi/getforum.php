<?php

/**
 * get a specific link
 * @poaram $args['lid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function xarbb_userapi_getforum($args)
{
    extract($args);

    if (empty($fid) && empty($fname)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    // Get link
    $query = "SELECT xar_fid,
                   xar_fname,
                   xar_fdesc,
                   xar_ftopics,
                   xar_fposts,
                   xar_fposter,
                   xar_fpostid
            FROM $xbbforumstable";
    if (!empty($fid) && is_numeric($fid)) {
        $query .= " WHERE xar_fid = " . xarVarPrepForStore($fid);
    } else {
        $query .= " WHERE xar_fname = '" . xarVarPrepForStore($fname) . "'";
    }

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($fid, $fname, $fdesc, $ftopics, $fposts, $fposter, $fpostid) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('ViewxarBB', 0, 'Forum',"$fid:All")) {
        return false;
    }
    $forum = array('fid'     => $fid,
                   'fname'   => $fname,
                   'fdesc'   => $fdesc,
                   'ftopics' => $ftopics,
                   'fposts'  => $fposts,
                   'fposter' => $fposter,
                   'fpostid' => $fpostid);

    return $forum;
}

?>