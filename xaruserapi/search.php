<?php
/*
 * Release Module
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
 * Searches all releases
 *
 * @author jojodee
 * @access private
 * @return array mixed description of return
 */
function release_userapi_search($args)
{
    if (empty($args) || count($args) < 1) {
        return;
    }
    extract($args);

     if($q == ''){
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $releasetable = & $xartable['release_id'];
    $where = '';
    $releases = array();
    $sql = "SELECT  xar_eid,
                    xar_rid,
                    xar_uid,
                    xar_regname,
                    xar_displname,
                    xar_desc,
                    xar_exttype
              FROM  $releasetable
              WHERE  (";

    $bindvars = array();

    if (isset($rid)) {
        $sql .= "xar_rid LIKE ?";
        $bindvars[] = $rid;
    }

    if (isset($uid)) {
        if (isset($rid)) {
            $sql .= " OR ";
        }
        $sql .= "xar_uid = ? ";
        $bindvars[] = $uid;
    }

    if (isset($regname)) {
        if (isset($rid) || isset($uid)) {
            $sql .= " OR ";
        }
        $sql .= " xar_regname LIKE ?";

        $bindvars[] = '%'.$regname.'%';
    }
   if (isset($displname)) {
        if (isset($rid) || isset($uid) || isset($regname)) {
            $sql .= " OR ";
        }
        $sql .= " xar_displname LIKE ?";
        $bindvars[] = '%'.$displname.'%';
    }
    if (isset($desc)) {
        if (isset($rid) || isset($uid) || isset($regname) || isset($displname)) {
            $sql .= " OR ";
        }
        $sql .= " xar_desc LIKE ?";
        $bindvars[] = '%'.$desc.'%';
    }
    $sql .= ")  ORDER BY xar_rid ASC";

    $result =& $dbconn->Execute($sql, $bindvars);
    if (!$result) return;
    // no results to return .. then return them :p
    if ($result->EOF) {
        return array();
    }
    for (; !$result->EOF; $result->MoveNext()) {
        list($eid, $rid, $uid, $regname, $displname, $desc, $exttype) = $result->fields;
        $exttype = $exttype == 0? xarML('Module') : xarML('Theme');
        if (xarSecurityCheck('ReadRelease', 0)) {
            $releases[] = array('eid' => $eid,
                                'rid' => $rid,
                                'uid' => $uid,
                                'regname' => $regname,
                                'displname' => $displname,
                                'desc' => $desc,
                                'author' => xarUserGetVar('name',$uid),
                                'exttype' => $exttype );
        }
    }
    $result->Close();

    // Return the releases
    return $releases;

}
?>