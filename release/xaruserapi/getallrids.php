<?php

/**
 * get all users
 * @returns array
 * @return array of users, or false on failure
 */
function release_userapi_getallrids($args)
{
    extract($args); 

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (!isset($idtypes)) {
        $idtypes = 1;
    }
    if ($idtypes == 3){
        $whereclause= "WHERE xar_type = 'module'";
    }elseif ($idtypes==2) {
        $whereclause= "WHERE xar_type = 'theme'";
    }else {
        $whereclause= "WHERE xar_type = 'theme' or xar_type = 'module'";
    }

    $releaseinfo = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_name,
                     xar_desc,
                     xar_type,
                     xar_certified,
                     xar_approved,
                     xar_rstate
            FROM $releasetable ".
            $whereclause."
            ORDER BY xar_rid";
    if (!empty($certified)) {
        $query .= " WHERE xar_certified = '" . xarVarPrepForStore($certified). "'";
    }

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $uid, $name, $desc, $type, $certified, $approved,$rstate) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rid'        => $rid,
                                   'uid'        => $uid,
                                   'name'       => $name,
                                   'desc'       => $desc,
                                   'type'       => $type,
                                   'certified'  => $certified,
                                   'approved'   => $approved,
                                   'rstate'     => $rstate);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

?>
