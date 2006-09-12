<?php
/*
 * Release module get all registered release ids
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
 * get all users
 * @returns array
 * @return array of users, or false on failure
 */
function release_userapi_getallids($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $releaseinfo = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];
    $bindvars=array();
    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_regname,
                     xar_displname,
                     xar_desc,
                     xar_type,
                     xar_class,
                     xar_certified,
                     xar_approved,
                     xar_rstate,
                     xar_regtime,
                     xar_modified,
                     xar_members,
                     xar_scmlink,
                     xar_openproj
            FROM $releasetable
            ORDER BY xar_rid";
    if (!empty($certified)) {
        $query .= " WHERE xar_certified = ?";
      $bindvars[]=$certified;
    }
    if (isset($type) and !empty($type)) {
        if (!empty($certified)) {
           $query .= " AND xar_type = ?";
        }else {
           $query .= " WHERE xar_type = ?";
        }
       $bindvars[]= $type;
    }

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1,$bindvars);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $uid, $regname, $displname, $desc, $type, $class, $certified, $approved, 
             $rstate, $regtime, $modified, $members, $scmlink,$openproj) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rid'        => $rid,
                                   'uid'        => $uid,
                                   'regname'    => $regname,
                                   'displname'  => $displname,
                                   'desc'       => $desc,
                                   'type'       => $type,
                                   'class'      => $class,
                                   'certified'  => $certified,
                                   'approved'   => $approved,
                                   'rstate'     => $rstate,
                                   'regtime'    => $regtime,
                                   'modified'   => $modified,
                                   'members'    => $members,
                                   'scmlink'    => $scmlink,
                                   'openproj'   => $openproj);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

?>