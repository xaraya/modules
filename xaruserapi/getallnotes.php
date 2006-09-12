<?php
/*
 * Get all release notes
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
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
    $bindvars=array();
    $where = '';
    $query = "SELECT rnotes.xar_rnid,
                     rnotes.xar_rid,
                     rids.xar_regname,
                     rids.xar_uid,
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
                     rnotes.xar_rstate,
                     rnotes.xar_usefeed,
                     rnotes.xar_exttype
            FROM $releasenotes as rnotes,$releaseids as rids
            WHERE rnotes.xar_rid=rids.xar_rid";

    if (!empty($certified)) {
        $where .= " AND rnotes.xar_certified = ?
                    AND rnotes.xar_approved = 2";
        $bindvars[] = $certified;
    } elseif (!empty($supported)) {
        $where .= " AND rnotes.xar_supported = ?
                    AND rnotes.xar_approved = 2";
        $bindvars[] = $supported;
    } elseif (!empty($price)) {
        $where .= " AND rnotes.xar_price = ?
                    AND rnotes.xar_approved = 2";
        $bindvars[] = $price;
    } elseif (!empty($rid)) {
        $where .= " AND rnotes.xar_rid = ?
                    AND rnotes.xar_approved = 2";
        $bindvars[] = $rid;
    }elseif (!empty($approved)) {
        $where .= " AND rnotes.xar_approved = ?";
        $bindvars[] = $approved;
    }
    if (!empty($usefeed)) {
        if (!empty($where)) {
            $where .= " AND rnotes.xar_usefeed = ?";
        } else{
            $where .= " AND rnotes.xar_usefeed = ?
                    AND rnotes.xar_approved = 2";
        }
        $bindvars[] = $usefeed;
    }

    $query .= $where;

    $query .= " ORDER by xar_time DESC";

            //ORDER BY xar_rnid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rnid, $rid, $regname, $uid,$version, $price, $priceterms, $demo, $demolink, $dllink, 
             $supported, $supportlink, $changelog, $notes, $time,  $enotes, $certified, $approved,$rstate,$usefeed,$exttype) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rnid'       => $rnid,
                                   'rid'        => $rid,
                                   'regname'    => $regname,
                                   'uid'        => $uid,
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
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

?>
