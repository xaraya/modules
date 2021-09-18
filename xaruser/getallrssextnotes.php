<?php
/**
 * Add a new extension
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Add an extension and request an ID
 *
 * @param enum phase Phase we are at
 *
 * @return array
 * @author Release module development team
 */
function release_userapi_getallrssextnotes($args)
{
    extract($args);

    $releaseinfo = [];

    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasenotes = $xartable['release_notes'];
    //We just want those approved and those that are required in the RSS feed
    $query = "SELECT xar_rnid,
                     xar_eid,
                     xar_rid,
                     xar_exttype,
                     xar_version
            FROM $releasenotes
            WHERE xar_approved = 2 and xar_usefeed = 1
            ORDER by xar_time DESC";

    $result =& $dbconn->Execute($query);
    if (!$result) {
        return;
    }

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        [$rnid, $eid, $rid, $exttype, $version] = $result->fields;
        if (xarSecurity::check('OverviewRelease', 0)) {
            $releaseinfo[] = ['rnid'       => $rnid,
                                   'eid'        => $eid,
                                   'rid'        => $rid,
                                   'exttype'    => $exttype,
                                   'version'    => $version, ];
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}
