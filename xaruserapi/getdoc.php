<?php
/*
 * Get document
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_userapi_getdoc($args)
{
    extract($args);

    if (!isset($rdid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    // Get link
    $query = "SELECT xar_rdid,
                     xar_eid,
                     xar_rid,
                     xar_title,
                     xar_docs,
                     xar_exttype,
                     xar_time,
                     xar_approved
            FROM $releasetable
            WHERE xar_rdid = ?";
    $result =& $dbconn->Execute($query,array($rdid));
    if (!$result) return;

    list($rdid, $eid,$rid, $title, $docs, $exttype, $time, $approved) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rdid'       => $rdid,
                         'eid'        => $eid,
                         'rid'        => $rid,
                         'title'      => $title,
                         'docs'       => $docs,
                         'exttype'       => $exttype,
                         'time'       => $time,
                         'approved'   => $approved);

    return $releaseinfo;
}
?>