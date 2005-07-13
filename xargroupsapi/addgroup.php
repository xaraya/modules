<?php

function xproject_groupsapi_addgroup($args)
{
    extract($args);

    if(!isset($gname)) {
	xarSessionSetVar('errormsg', _MODARGSERROR);
	return false;
    }
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];

    if (!xarSecAuthAction(0, 'Groups::', "$gname::", ACCESS_ADD)) {
	xarSessionSetVar('errormsg', _GROUPSNOAUTH);
        return false;
    }

    // Confirm that this group does not already exist
    $query = "SELECT COUNT(*) FROM $groupstable
              WHERE xar_name = \"$gname\"";

    $result = $dbconn->Execute($query);

    list($count) = $result->fields;
    $result->Close();

    if ($count == 1) {
        xarSessionSetVar('errormsg', _GROUPALREADYEXISTS);
	return false;
    } else {
        $nextId = $dbconn->GenId($grouptable);
        $query = "INSERT INTO $groupstable
                  VALUES ($nextId, \"$gname\")";

        $dbconn->Execute($query);

	return true;
    }
}

?>