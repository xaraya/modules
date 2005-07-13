<?php
//
// ----------------------------------------------------------------------
// PostNuke Content Management System
// Copyright (C) 2001 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file: Group admin api
// ----------------------------------------------------------------------

/**
 * viewallgroups - generate all groups listing.
 * @param none
 * @return groups listing of available groups
 */
function xproject_groupsapi_getall()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];

    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_OVERVIEW)) {
	xarSessionSetVar('errormsg', _GROUPSNOAUTH);
        return false;
    }

    $groups = array();

    // Get and display current groups
    $query = "SELECT xar_gid,
                     xar_name
              FROM $groupstable
              ORDER BY xar_name";
    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() !=0) {
		xarSessionSetVar('errormsg', 'Error getting groups.');
		return false;
    }
	
    for(; !$result->EOF; $result->MoveNext()) {
		list($gid, $name) = $result->fields;
		
		$groupmembers = xarModAPIFunc('xproject','groups','getmembers',array('gid' => $gid));
		$memberlist = array();
		foreach($groupmembers as $member) $memberlist[] = $member['uid'];
		
		if(in_array(xarSessionGetVar('uid'),$memberlist) ||
			(xarSecAuthAction(0, 'Groups::', "$name::$gid", ACCESS_OVERVIEW))) {
			$groups[] = array('gid'  => $gid,
							  'name' => $name);
		}
    }
	
    $result->Close();
	
    return $groups;
}


/*
 * viewgroup - view users in group
 * @param $args['gid'] group id
 * @return $users array containing uname, uid
 */
function xproject_groupsapi_get($args)
{
    extract($args);

    if (!isset($gid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR2);
        return false;
    }

    if (!xarSecAuthAction(0, 'groups::', "::", ACCESS_READ)) {
        xarSessionSetVar('errormsg', _XPROJECTNOAUTH);
        return false;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];
    $groupscolumn = &$xartable['groups_column'];

    $query = "SELECT xar_gid,
                     xar_name
              FROM $groupstable
            WHERE $groupscolumn[gid] = " . $gid;
    $result = $dbconn->Execute($query);

    if ($dbconn->ErrorNo() != 0) {
        xarSessionSetVar('errormsg', $query);
        return false;
    }

    if ($result->EOF) {
        xarSessionSetVar('errormsg', $query);
        return false;
    }

	list($gid, $gname) = $result->fields;

    $result->Close();

	$groupmembers = xarModAPIFunc('xproject','groups','getmembers',array('gid' => $gid));
	$memberlist = array();
	foreach($groupmembers as $member) $memberlist[] = $member['uid'];
	
	if(in_array(xarSessionGetVar('uid'),$memberlist)
		|| (xarSecAuthAction(0, 'Groups::', "$gname::$gid", ACCESS_READ))) {
		$group = array('gid'	=> $gid,
					'gname'		=> $gname);
	}
	

    return $group;
}

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

/**
 * utility function to count the number of items held by this module
 *
 * @author the Example module development team
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function xproject_groupsapi_countitems()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];

    $sql = "SELECT COUNT(1)
            FROM $groupstable";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('Database error for #(1) function #(2)() in module #(3)',
                    'user', 'countitems', 'groups');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

/*
 * addGroup - add a group
 * @param $args['gname'] group name to add
 * @return true on success, false if group exists
 */
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

/**
 * viewallgroups - generate all groups listing.
 * @param none
 * @return groups listing of available groups
 */
function xproject_groupsapi_viewallgroups()
{
    if (!xarModAPILoad('groups', 'user')) {
	    $groups = xarModAPIFunc('xproject','groups','getall');
    } else {
	    $groups = xarModAPIFunc('groups','user','getall');
	}

    return $groups;
}


/*
 * deletegroup - delete a group & info
 * @param $args['gid']
 * @return true on success, false otherwise
 */
function xproject_groupsapi_deletegroup($args)
{
    extract($args);

    if(!isset($gid)) {
	xarSessionSetVar('errormsg', _MODARGSERROR);
	return false;
    }
    if (!xarSecAuthAction(0, 'Groups::', "$gname::$gid", ACCESS_EDIT)) {
	xarSessionSetVar('errormsg', _GROUPSNOAUTH);
        return false;
    }
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];
    $groupmembership = $xartable['group_membership'];
    $groupperms = $xartable['group_perms'];

    // Delete permissions for the group
    $query = "DELETE FROM $groupperms
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $dbconn->Execute($query);

    // Delete membership of the group
    $query = "DELETE FROM $groupmembership
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $dbconn->Execute($query);

    // Delete the group itself
    $query = "DELETE FROM $groupstable
              WHERE xar_gid=".xarVarPrepForStore($gid)."";
    $dbconn->Execute($query);

    return true;
}

/*
 * renamegroup - rename a group
 * @param $args['gid'] group id
 * @param $args['gname'] group name
 * @return true on success, false on failure.
 */
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
function xproject_groupsapi_viewgroup($args)
{
//    if (!xarModAPILoad('groups', 'user')) {
	    $users = xarModAPIFunc('xproject','groups','get');
//    } else {
//	    $users = xarModAPIFunc('groups','user','get');
//	}

    return $users;
}

/*
 * deleteuser - delete a user from a group
 * @param $args['gid'] group id
 * @param $args['uid'] user id
 * @return true on success, false on failure
 */
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