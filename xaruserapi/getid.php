<?php
/*
 * Get an extension ID
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_userapi_getid($args)
{
    extract($args);

    if (!isset($rid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Get link
    $query = "SELECT xar_rid,
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
            FROM $releasetable
            WHERE xar_rid = ?";
    $result =& $dbconn->Execute($query,array($rid));
    if (!$result) return;

    list($rid, $uid, $regname, $displname, $desc, $class, $certified, $approved,
         $rstate, $regtime, $modified, $members, $scmlink, $openproj, $exttype ) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rid'        => $rid,
                         'uid'        => $uid,
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
                         'exttype'    => $exttype);

    return $releaseinfo;
}
?>