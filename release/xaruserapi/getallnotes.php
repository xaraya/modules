<?php

function release_userapi_getallnotes($args)
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

    $releasenotes = $xartable['release_notes'];

    $query = "SELECT xar_rnid,
                     xar_rid,
                     xar_version,
                     xar_price,
                     xar_priceterms,
                     xar_demo,
                     xar_demolink,
                     xar_dllink,
                     xar_supported,
                     xar_supportlink,
                     xar_changelog,
                     xar_notes,
                     xar_time,
                     xar_enotes,
                     xar_certified,
                     xar_approved
            FROM $releasenotes";
    if (!empty($approved)) {
        $query .= " WHERE xar_approved = '" . xarVarPrepForStore($approved). "'
                    ORDER by xar_time DESC";
    } elseif (!empty($certified)) {
        $query .= " WHERE xar_certified = '" . xarVarPrepForStore($certified) . "'
                    AND xar_approved = 2
                    ORDER by xar_time DESC";
    } elseif (!empty($supported)) {
        $query .= " WHERE xar_supported = '" . xarVarPrepForStore($supported) . "'
                    AND xar_approved = 2
                    ORDER by xar_time DESC";
    } elseif (!empty($price)) {
        $query .= " WHERE xar_price = '" . xarVarPrepForStore($price) . "'
                    AND xar_approved = 2
                    ORDER by xar_time DESC";
    } elseif (!empty($rid)) {
        $query .= " WHERE xar_rid = '" . xarVarPrepForStore($rid) . "'
                    AND xar_approved = 2
                    ORDER by xar_time DESC";
    }

            //ORDER BY xar_rnid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rnid, $rid, $version, $price, $priceterms, $demo, $demolink, $dllink, $supported, $supportlink, $changelog, $notes, $time,  $enotes, $certified, $approved) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rnid'       => $rnid,
                                   'rid'        => $rid,
                                   'version'    => $version,
                                   'price'      => $price,
                                   'priceterms' => $priceterms,
                                   'demo'       => $demo,
                                   'demolink'   => $demolink,
                                   'dllink'     => $dllink,
                                   'supported'  => $supported,
                                   'supportlink'=> $supportlink,
                                   'changelog'  => $changelog,
                                   'notes'      => $notes,
                                   'time'       => $time,
                                   'enotes'     => $enotes,
                                   'certified'  => $certified,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

?>