<?php


function release_userapi_getallrssextnotes($args)
{
    extract($args);

    $releaseinfo = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasenotes = $xartable['release_notes'];
    //jojodeeWe want
    $query = "SELECT xar_rnid,
                     xar_rid,
                     xar_type,
                     xar_version
            FROM $releasenotes
            WHERE xar_approved = 2
            ORDER by xar_time DESC";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rnid, $rid, $type, $version) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rnid'       => $rnid,
                                   'rid'        => $rid,
                                   'type'       => $type,
                                   'version'    => $version);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

?>
