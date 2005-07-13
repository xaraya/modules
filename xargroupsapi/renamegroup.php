<?php

function xproject_groupsapi_renamegroup($args)
{
    extract($args);

    if((!isset($gid)) || (!isset($gname))) {
	xarSessionSetVar('errormsg', _MODARGSERROR);
	return false;
    }
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];

    // Get details on current group
    $query = "SELECT xar_name
              FROM $groupstable
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $result = $dbconn->Execute($query);

    if ($result->EOF) {
        xarSessionSetVar('errormsg', 'No such group ID '.$gid.'');
	return false;
    }
    list($oldgname) = $result->fields;
    $result->Close();

    if (!xarSecAuthAction(0, 'Groups::', "$oldgname::$gid", ACCESS_EDIT)) {
        xarSessionSetVar('errormsg', _GROUPSEDITNOAUTH);
        return false;
    }
    $query = "UPDATE $groupstable
              SET xar_name=\"$gname\"
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $dbconn->Execute($query);

    return true;
}

/*
 * viewgroup - view users in group
 * @param $args['gid'] group id
 * @return $users array containing uname, uid
 */
?>