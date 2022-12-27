<?php
/*
 * Get an extension ID
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_userapi_getid($args)
{
    extract($args);

    if (!isset($eid) && (!isset($rid))) {
        throw new BadParameterException(null, xarML('Invalid Parameter Count'));
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasetable = $xartable['release_id'];

    // Get link
    $query = "SELECT DISTINCT xar_eid,
                     xar_rid,
                     xar_uid,
                     xar_regname,
                     xar_displname,
                     xar_desc,
                     xar_class,
                     xar_certified,
                     xar_approved,
                     xar_rstate,
                     xar_regtime,
                     xar_modified,
                     xar_members,
                     xar_scmlink,
                     xar_openproj,
                     xar_exttype
            FROM $releasetable ";
    if (isset($eid)) {
        $query .= "WHERE xar_eid = ?";
        $bindvars = [$eid];
    } elseif (isset($rid) && isset($exttype) && !empty($exttype)) {
        $query .= "WHERE xar_rid = ? AND xar_exttype = ?";
        $bindvars = [(int)$rid,(int)$exttype];
    } elseif (isset($rid) && (!isset($exttype) || empty($exttype))) { //legacy check
        //try modules and themes for backward compatibility
        $query .= "WHERE xar_rid = ? ";
        $bindvars = [(int)$rid];
    }

    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) {
        return;
    }

    [$eid,$rid, $uid, $regname, $displname, $desc, $class, $certified, $approved,
        $rstate, $regtime, $modified, $members, $scmlink, $openproj, $exttype] = $result->fields;
    $result->Close();

    if (!xarSecurity::check('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = ['eid'        => (int)$eid,
                         'rid'        => (int)$rid,
                         'uid'        => (int)$uid,
                         'regname'    => $regname,
                         'displname'  => $displname,
                         'desc'       => $desc,
                         'class'      => $class,
                         'certified'  => $certified,
                         'approved'   => $approved,
                         'rstate'     => $rstate,
                         'regtime'    => $regtime,
                         'modified'   => $modified,
                         'members'    => $members,
                         'scmlink'    => $scmlink,
                         'openproj'   => $openproj,
                         'exttype'    => (int)$exttype, ];

    return $releaseinfo;
}
