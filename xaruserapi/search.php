<?php
/*
 * Release Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @author Release Module Development team
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
    $sql = "SELECT  xar_rid,
                    xar_uid,
                    xar_regname,
                    xar_displname,
                    xar_desc,
                    xar_type
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
        if (isset($rid) || isset($tid)) {
            $sql .= " OR ";
        }
        $sql .= " xar_regname LIKE ?";

        $bindvars[] = '%'.$regname.'%';
    }
   if (isset($displname)) {
        if (isset($rid) || isset($tid) || isset($regname)) {
            $sql .= " OR ";
        }
        $sql .= " xar_displname LIKE ?";
        $bindvars[] = '%'.$displname.'%';
    }
    if (isset($desc)) {
        if (isset($rid) || isset($tid) || isset($regname) || isset($displname)) {
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
        list($rid, $uid, $regname, $displname, $desc, $exttype) = $result->fields;
        $exttype = $exttype == 0? xarML('Module') : xarML('Theme');
        if (xarSecurityCheck('ReadRelease', 0)) {
            $releases[] = array('rid' => $rid,
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
