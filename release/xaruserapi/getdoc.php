<?php

function release_userapi_getdoc($args)
{
    extract($args);

    if (!isset($rdid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    // Get link
    $query = "SELECT xar_rdid,
                     xar_rid,
                     xar_title,
                     xar_docs,
                     xar_type,
                     xar_time,
                     xar_approved
            FROM $releasetable
            WHERE xar_rdid = " . xarVarPrepForStore($rdid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($rdid, $rid, $title, $docs, $type, $time, $approved) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rdid'       => $rdid,
                         'rid'        => $rid,
                         'title'      => $title,
                         'docs'       => $docs,
                         'type'       => $type,
                         'time'       => $time,
                         'approved'   => $approved);

    return $releaseinfo;
}

?>