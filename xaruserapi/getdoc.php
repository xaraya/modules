<?php
/*
 * Get document
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_userapi_getdoc($args)
{
    extract($args);

    if (!isset($rdid)) {
        throw new BadParameterException(null, xarML('Invalid Parameter Count'));
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

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
    $result =& $dbconn->Execute($query, [$rdid]);
    if (!$result) {
        return;
    }

    [$rdid, $eid, $rid, $title, $docs, $exttype, $time, $approved] = $result->fields;
    $result->Close();

    if (!xarSecurity::check('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = ['rdid'       => $rdid,
                         'eid'        => $eid,
                         'rid'        => $rid,
                         'title'      => $title,
                         'docs'       => $docs,
                         'exttype'       => $exttype,
                         'time'       => $time,
                         'approved'   => $approved, ];

    return $releaseinfo;
}
