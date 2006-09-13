<?php
/**
 * Get a releae note
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/*
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * @TODO
 */
function release_userapi_getnote($args)
{
    extract($args);

    if (!isset($rnid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_notes'];

    // Get link
    $query = "SELECT xar_rnid,
                     xar_eid,
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
                     xar_approved,
                     xar_rstate,
                     xar_usefeed,
                     xar_exttype
            FROM $releasetable
            WHERE xar_rnid = ?";
    $result =& $dbconn->Execute($query,array($rnid));
    if (!$result) return;

    list($rnid, $eid,$rid, $version, $price, $priceterms, $demo, $demolink, $dllink, $supported, $supportlink,
        $changelog, $notes, $time, $enotes, $certified, $approved,$rstate,$usefeed,$exttype) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rnid'       => (int)$rnid,
                         'eid'        => (int)$eid,
                         'rid'        => (int)$rid,
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
                         'rstate'     => $rstate,
                         'usefeed'    => $usefeed,
                         'exttype'    => $exttype);

    return $releaseinfo;
}

?>