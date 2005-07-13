<?php

function xproject_groupsapi_getmembers($args)
{
    extract($args);

	// NEED TO PULL GROUP NAME FOR SECAUTH CALL
	if (!xarSecAuthAction(0, 'Groups::', '::', ACCESS_READ)) {
    	xarSessionSetVar('errormsg', _GROUPSNOAUTH);
        return false;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $userstable = $xartable['users'];
    $groupmembership = $xartable['group_membership'];

    $users = array();
    // Get users in this group
    $query = "SELECT DISTINCT xar_uid
              FROM $groupmembership";

	if(isset($gid)) $query .= " WHERE xar_gid = ".xarVarPrepForStore($gid)."";
	elseif(isset($eid)) {
		$query .= " WHERE xar_gid = ".xarVarPrepForStore($eid)."";
		$exclude = " NOT";
	}

    $result = $dbconn->Execute($query);
    if (!$result->EOF) {
        for(;list($uid) = $result->fields;$result->MoveNext() ) {
            $uids[] = $uid;
        }
        $result->Close();
        $uidlist=implode(",", $uids);
	
        // Get names of users
        $query = "SELECT xar_uname,
                         xar_uid
                  FROM $userstable
                  WHERE xar_uid" . $exclude . " IN ($uidlist)
                  ORDER BY xar_uname";
        $result = $dbconn->Execute($query);

        while(list($uname, $uid) = $result->fields) {
            $result->MoveNext();
			$users[] = array('uname' => $uname,
					 'uid'   => $uid);
        }
        $result->Close();
    }
    return $users;
}

?>