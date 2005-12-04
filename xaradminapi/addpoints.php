<?php
/**
 * Get the score for a user
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints Module Development Team
 */
function userpoints_adminapi_addpoints($args)
{
    extract($args);

    if (empty($uid) || !is_numeric($uid) ||
        empty($points) || !is_numeric($points)) {
        return;
    }

    //add the points to the table.
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $scoretable = $xartable['userpoints_score'];

    $oldscore = xarModAPIFunc('userpoints','user','get',
                              array('uid' => $uid));

    // FIXME: score is currently saved as x 100 (bigint)
    $points = (int) (100 * $points);

    if (!isset($oldscore)) {
        $nextId = $dbconn->GenId($scoretable);
        $sql = "INSERT INTO $scoretable (
                                     xar_id,
                                     xar_authorid,
                                     xar_totalscore)
                VALUES(?,?,?)";
        $bindvars = array($nextId, (int)$uid, (int)$points);
        $result =& $dbconn->Execute($sql, $bindvars);
        if (!$result) return;
    } else {
        // can't use bind vars for field = field + ? (I think)
        $sql = "UPDATE $scoretable
                SET xar_totalscore = xar_totalscore + $points
                WHERE xar_authorid = ?";
        $bindvars = array((int)$uid);
        $result =& $dbconn->Execute($sql, $bindvars);
        if (!$result) return;
    }


/*
    // TODO: add points for the module item as well ?

    //add the points to the table.
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pointstable = $xartable['userpoints'];

    // Get next ID in table
    $nextId = $dbconn->GenId($pointstable);
          $sql = "INSERT INTO $pointstable (
                                     xar_upid,
                                     xar_moduleid,
                                     xar_itemtype,
                                     xar_objectid,
                                     xar_status,
                                     xar_authorid,
                                     xar_pubdate,
                                     xar_cpoints)
                  VALUES(?,?,?,?,?,?,?,?)";
            $bindvars = array((int)$nextId, (int)$moduleid, $itemtype, $objectid, $status,
                                   $authorid, $pubdata, $points);
            $result =& $dbconn->Execute($sql, $bindvars);
            if (!$result) return;


*/
    // Return success
    return true;
}
?>
