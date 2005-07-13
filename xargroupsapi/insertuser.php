<?php

function xproject_groupsapi_insertuser($args)
{
    extract($args);

    if((!isset($gid)) || (!isset($uid))) {
		xarSessionSetVar('errormsg', _MODARGSERROR);
		return false;
    }
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupmembership = $xartable['group_membership'];

    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_ADD)) {
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

    $query = "INSERT INTO $groupmembership
              (xar_uid,
               xar_gid)
              VALUES
              (".xarVarPrepForStore($uid).",
               ".xarVarPrepForStore($gid).")";
    $dbconn->Execute($query);

    return true;
}
?>