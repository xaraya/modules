<?php

function release_userapi_getdocs($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $releasedocs = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasedocstable = $xartable['release_docs'];

    $query = "SELECT xar_rdid,
                     xar_rid,
                     xar_title,
                     xar_docs,
                     xar_type,
                     xar_time,
                     xar_approved
            FROM $releasedocstable
                         
                     /*";
    if (!empty($apporved)) {
        $query .= " WHERE xar_rid = '" . xarVarPrepForStore($rid) . "'
                    AND xar_approved = '" . xarVarPrepForStore($approved) . "'
                    AND xar_type = '" . xarVarPrepForStore($type) . "'";
    } elseif(empty($type)) {
        $query .= " WHERE xar_approved = '" . xarVarPrepForStore($approved) . "'";
    } else {
        $query .= "*/ WHERE xar_rid = '" . xarVarPrepForStore($rid) . "'
                    AND xar_type = '" . xarVarPrepForStore($type) . "'";
    }

    $query .= "ORDER BY xar_rdid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rdid, $rid, $title, $docs, $type, $time, $approved) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releasedocs[] = array('rdid'       => $rdid,
                                   'rid'        => $rid,
                                   'title'      => $title,
                                   'docs'       => $docs,
                                   'type'       => $type,
                                   'time'       => $time,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releasedocs;

}

?>