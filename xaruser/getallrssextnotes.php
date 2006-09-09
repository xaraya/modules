<?php
/**
 * Add a new extension
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
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

    $releaseinfo = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasenotes = $xartable['release_notes'];
    //We just want those approved and those that are required in the RSS feed
    $query = "SELECT xar_rnid,
                     xar_rid,
                     xar_type,
                     xar_version
            FROM $releasenotes
            WHERE xar_approved = 2 and xar_usefeed = 1
            ORDER by xar_time DESC";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rnid, $rid, $type, $version) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rnid'       => $rnid,
                                   'rid'        => $rid,
                                   'type'       => $type,
                                   'version'    => $version);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

?>