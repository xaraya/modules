<?php

function release_userapi_getthemeids($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $releaseinfo = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_name,
                     xar_desc,
                     xar_type,
                     xar_certified,
                     xar_approved
            FROM $releasetable
            WHERE xar_type = 'theme'
            ORDER BY xar_rid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $uid, $name, $desc, $type, $certified, $approved) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rid'        => $rid,
                                   'uid'        => $uid,
                                   'name'       => $name,
                                   'desc'       => $desc,
                                   'type'       => $type,
                                   'certified'  => $certified,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

?>