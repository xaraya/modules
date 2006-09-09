<?php
/**
 * Get documents
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
 * Get the docs
 * 
 * @param $rnid $rid $approved
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * 
 */
function release_userapi_getdocs($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $releasedocs = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasedocstable = $xartable['release_docs'];

    $bindvars = array();
    $query = "SELECT xar_rdid,
                     xar_rid,
                     xar_title,
                     xar_docs,
                     xar_type,
                     xar_time,
                     xar_approved
            FROM $releasedocstable

                     /*";
    if (!empty($apporved)) {
        $query .= " WHERE xar_rid = ? AND xar_approved = ? AND xar_type = ?";
        array_push($bindvars, $rid, $approved, $type);
    } elseif(empty($type)) {
        $query .= " WHERE xar_approved = ?";
        array_push($bindvars, $approved);
    } else {
        $query .= "*/ WHERE xar_rid = ? AND xar_type = ?";
        array_push($bindvars, $rid, $type);
    }

    $query .= " ORDER BY xar_rdid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rdid, $rid, $title, $docs, $type, $time, $approved) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releasedocs[] = array('rdid'       => $rdid,
                                   'rid'        => $rid,
                                   'title'      => $title,
                                   'docs'       => $docs,
                                   'type'       => $type,
                                   'time'       => $time,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
return $releasedocs;

}

?>