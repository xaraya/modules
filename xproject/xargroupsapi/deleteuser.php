<?php

function xproject_groupsapi_deleteuser($args)
{
    extract($args);

    if((!isset($gid)) || (!isset($uid))) {
	xarSessionSetVar('errormsg', _MODARGSERROR);
	return false;
    }
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupmembership = $xartable['group_membership'];

    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_DELETE)) {
	xarSessionSetVar('errormsg', _GROUPSNOAUTH);
        return false;
    }
    // Get details on current group
    $query = "SELECT xar_name
              FROM $xartable[groups]
              WHERE xar_gid=".xarVarPrepForStore($gid)."";

    $result = $dbconn->Execute($query);
    if ($result->EOF) {
	xarSessionSetVar('errormsg', 'No such group ID '.$gid.'');
	return false;
    }
    list($gname) = $result->fields;
    $result->Close();

    $query = "DELETE FROM $groupmembership
              WHERE xar_uid=".xarVarPrepForStore($uid)."
                AND xar_gid=".xarVarPrepForStore($gid)."";
    $dbconn->Execute($query);

    return true;
}

/*
 * insertuser - add a user to a group
 * @param $args['uid'] user id
 * @param $args['gid'] group id
 * @return true on succes, false on failure
 */
?>