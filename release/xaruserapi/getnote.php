<?php

function release_userapi_getnote($args)
{
    extract($args);

    if (!isset($rnid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_notes'];

    // Get link
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
            FROM $releasetable
            WHERE xar_rnid = " . xarVarPrepForStore($rnid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($rnid, $rid, $version, $price, $priceterms, $demo, $demolink, $dllink, $supported, $supportlink, $changelog, $notes, $time, $enotes, $certified, $approved) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rnid'       => $rnid,
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

    return $releaseinfo;
}

?>