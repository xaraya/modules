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
    $releaseids = $xartable['release_id'];

    $query = "SELECT rnotes.xar_rnid,
                     rnotes.xar_rid,
                     rids.xar_regname,
                     rnotes.xar_version,
                     rnotes.xar_price,
                     rnotes.xar_priceterms,
                     rnotes.xar_demo,
                     rnotes.xar_demolink,
                     rnotes.xar_dllink,
                     rnotes.xar_supported,
                     rnotes.xar_supportlink,
                     rnotes.xar_changelog,
                     rnotes.xar_notes,
                     rnotes.xar_time,
                     rnotes.xar_enotes,
                     rnotes.xar_certified,
                     rnotes.xar_approved,
                     rnotes.xar_rstate
            FROM $releasenotes as rnotes,$releaseids as rids";
    if (!empty($approved)) {
        $query .= " WHERE rnotes.xar_rid=rids.xar_rid AND rnotes.xar_approved = '" . xarVarPrepForStore($approved). "'";
    } elseif (!empty($certified)) {
        $query .= " WHERE rnotes.xar_rid=rids.xar_rid AND rnotes.xar_certified = '" . xarVarPrepForStore($certified) . "'
                    AND rnotes.xar_approved = 2";
    } elseif (!empty($supported)) {
        $query .= " WHERE rnotes.xar_rid=rids.xar_rid AND rnotes.xar_supported = '" . xarVarPrepForStore($supported) . "'
                    AND rnotes.xar_approved = 2";
    } elseif (!empty($price)) {
        $query .= " WHERE rnotes.xar_rid=rids.xar_rid AND rnotes.xar_price = '" . xarVarPrepForStore($price) . "'
                    AND rnotes.xar_approved = 2";
    } elseif (!empty($rid)) {
        $query .= " WHERE rnotes.xar_rid=rids.xar_rid AND rnotes.xar_rid = '" . xarVarPrepForStore($rid) . "'
                    AND rnotes.xar_approved = 2";
    } else {
        $query .= " WHERE rnotes.xar_rid=rids.xar_rid";
    }
    $query .= " ORDER by xar_time DESC";

            //ORDER BY xar_rnid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rnid, $rid, $regname, $version, $price, $priceterms, $demo, $demolink, $dllink, $supported, $supportlink, $changelog, $notes, $time,  $enotes, $certified, $approved,$rstate) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rnid'       => $rnid,
                                   'rid'        => $rid,
                                   'regname'      => $regname,
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
                                   'approved'   => $approved,
                                   'rstate'     => $rstate);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

?>