<?php // $Id$
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Volodymyr Metenchuk
// Purpose of file:  Todolist user API
// ----------------------------------------------------------------------

/**
 * get all projects
 * @returns array
 * @return array of items, or false on failure
 */
function todolist_userapi_getallprojects($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    if ((!isset($startnum)) || (!isset($numitems))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    $items = array();

    if (!pnSecAuthAction(0, 'todolist::', '::', ACCESS_READ)) {
        return $items;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_projects_column = &$pntable['todolist_projects_column'];

    $sql = "SELECT $todolist_projects_column[id],$todolist_projects_column[project_name], 
         $todolist_projects_column[description],$todolist_projects_column[project_leader]
         FROM $pntable[todolist_projects] ORDER BY $todolist_projects_column[project_name]";

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', xarML('Items load failed'));
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list($pid, $pname, $pdescription,$pleader) = $result->fields;
        if (pnSecAuthAction(0, 'todolist::', "$pname::$pid", ACCESS_READ)) {
            $items[] = array('project_id' => $pid,
                             'project_name' => $pname,
                             'project_description' => $pdescription,
                             'project_leader' => $pleader);
        }
    }

    $result->Close();
    return $items;
}

/**
 * get a specific project
 * @param $args['project_id'] id of project to get
 * @returns array
 * @return item array, or false on failure
 */
function todolist_userapi_getproject($args)
{
    extract($args);

    if (!isset($project_id)) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_projects_column = &$pntable['todolist_projects_column'];

    $sql = "SELECT $todolist_projects_column[id],$todolist_projects_column[project_name], 
         $todolist_projects_column[description],$todolist_projects_column[project_leader]
         FROM $pntable[todolist_projects] WHERE $todolist_projects_column[id] = ".
         pnVarPrepForStore($project_id);

    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    if ($result->EOF) {
        return false;
    }

    list($pid, $pname, $pdescription,$pleader) = $result->fields;

    $result->Close();

    if (!pnSecAuthAction(0, 'todolist::', "$pname::$pid", ACCESS_READ)) {
        return false;
    }

    $item = array('project_id' => $pid,
                  'project_name' => $pname,
                  'project_description' => $pdescription,
                  'project_leader' => $pleader);
    return $item;
}

/**
 * function to count the number of projects
 * @returns integer
 * @return number of items held by this module
 */
function todolist_userapi_countprojects()
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $sql = "SELECT COUNT(1) FROM $pntable[todolist_projects]";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

/**
 * get all groups
 * @returns array
 * @return array of items, or false on failure
 */
function todolist_userapi_getallgroups($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    if ((!isset($startnum)) || (!isset($numitems))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    $items = array();

    if (!pnSecAuthAction(0, 'todolist::', '::', ACCESS_READ)) {
        return $items;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_groups_column = &$pntable['todolist_groups_column'];

    $sql = "SELECT $todolist_groups_column[id],$todolist_groups_column[group_name], 
         $todolist_groups_column[description],$todolist_groups_column[group_leader]
         FROM $pntable[todolist_groups] ORDER BY $todolist_groups_column[group_name]";

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', xarML('Items load failed'));
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list($gid, $gname, $gdescription,$gleader) = $result->fields;
        if (pnSecAuthAction(0, 'todolist::', "$gname::$gid", ACCESS_READ)) {
            $items[] = array('group_id' => $gid,
                             'group_name' => $gname,
                             'group_description' => $gdescription,
                             'group_leader' => $gleader);
        }
    }

    $result->Close();
    return $items;
}

/**
 * get a specific group
 * @param $args['group_id'] id of group to get
 * @returns array
 * @return item array, or false on failure
 */
function todolist_userapi_getgroup($args)
{
    extract($args);

    if (!isset($group_id)) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_groups_column = &$pntable['todolist_groups_column'];

    $sql = "SELECT $todolist_groups_column[id],$todolist_groups_column[group_name], 
         $todolist_groups_column[description],$todolist_groups_column[group_leader]
         FROM $pntable[todolist_groups] WHERE $todolist_groups_column[id] = ".
         pnVarPrepForStore($group_id);

    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    if ($result->EOF) {
        return false;
    }

    list($gid, $gname, $gdescription,$gleader) = $result->fields;

    $result->Close();

    if (!pnSecAuthAction(0, 'todolist::', "$gname::$gid", ACCESS_READ)) {
        return false;
    }

    $item = array('group_id' => $gid,
                  'group_name' => $gname,
                  'group_description' => $gdescription,
                  'group_leader' => $gleader);
    return $item;
}

/**
 * function to count the number of groups
 * @returns integer
 * @return number of items held by this module
 */
function todolist_userapi_countgroups()
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $sql = "SELECT COUNT(1) FROM $pntable[todolist_groups]";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

/**
 * get all users
 * @returns array
 * @return array of items, or false on failure
 */
function todolist_userapi_getallusers($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    if ((!isset($startnum)) || (!isset($numitems))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    $items = array();

    if (!pnSecAuthAction(0, 'todolist::', '::', ACCESS_READ)) {
        return $items;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_project_members_column = &$pntable['todolist_project_members_column'];    
    $sql = "SELECT DISTINCT $todolist_project_members_column[member_id] FROM $pntable[todolist_project_members]";
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', xarML('Items load failed'));
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list($uid) = $result->fields;
        $userpref = xarModGetUserVar('todolist','userpref',$uid);
        list($unotify, $u1project, $umytasks, $ushowicons) = explode(';',$userpref);
        if (pnSecAuthAction(0, 'todolist::', "::$uid", ACCESS_READ)) {
            $items[] = array('user_id' => $uid,
                             'user_email_notify' => $unotify,
                             'user_primary_project' => $u1project,
                             'user_my_tasks' => $umytasks,
                             'user_show_icons' => $ushowicons);
        }
    }

    $result->Close();
    return $items;
}

/**
 * get a specific user
 * @param $args['user_id'] id of project to get
 * @returns array
 * @return item array, or false on failure
 */
function todolist_userapi_getuser($args)
{
    extract($args);

    if (!isset($user_id)) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    $userpref = xarModGetUserVar('todolist','userpref',$user_id);
    if (!empty($userpref)) {
        list($unotify, $u1project, $umytasks, $ushowicons) = explode(';',$userpref);
    } else {
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::$user_id", ACCESS_READ)) {
        return false;
    }

    $item = array('user_id' => $user_id,
                  'user_email_notify' => $unotify,
                  'user_primary_project' => $u1project,
                  'user_my_tasks' => $umytasks,
                  'user_show_icons' => $ushowicons);
    return $item;
}

/**
 * function to count the number of users
 * @returns integer
 * @return number of items held by this module
 */
function todolist_userapi_countusers()
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_project_members_column = &$pntable['todolist_project_members_column'];    

    // FIXME convert query to COUNT
    // $sql = "SELECT COUNT(1) $todolist_project_members_column[member_id] FROM $pntable[todolist_project_members]";
    // $result = $dbconn->Execute($sql);
    // list($numitems) = $result->fields;

    $sql = "SELECT DISTINCT $todolist_project_members_column[member_id] FROM $pntable[todolist_project_members]";
    $result = $dbconn->Execute($sql);
    $numitems = $result->PO_RecordCount();

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    $result->Close();

    return $numitems;
}

function todolist_userapi_updateuser($args)
{
    extract($args);
    
    if ((!isset($user_id)) && (!isset($user_email_notify)) && (!isset($user_primary_project)) &&
        (!isset($user_my_tasks)) && (!isset($user_show_icons))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }
    //HTML-Forms submit nothing if a checkbox isn't checked... :-(
    if (!isset($user_email_notify)) { $user_email_notify = 0; }
    if (!isset($user_primary_project)) { $user_primary_project = 0; }
    if (!isset($user_my_tasks)) { $user_my_tasks = 0; }
    if (!isset($user_show_icons)) { $user_show_icons = 0; }

    if ($user_id != pnUserGetVar('uid')) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $userpref = $new_email_notify.';'.$new_primary_project.';'.$new_my_tasks.';'.$showicons;
    xarModSetUserVar('todolist','userpref',$userpref,$user_id);

    pnSessionSetVar('errormsg', xarML('User info was updated'));
    return true;
}

/**
 * get a specific project members
 * @param $args['project_id'] id of project to get
 * @returns array
 * @return item array, or false on failure
 */
function todolist_userapi_getprojectmembers($args)
{
    extract($args);

    if (!isset($project_id)) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::$project_id", ACCESS_READ)) {
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $sql = "SELECT $todolist_project_members_column[member_id]
        FROM $pntable[todolist_project_members]
        WHERE $todolist_project_members_column[project_id]=".pnVarPrepForStore($project_id);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    $project_members = array();
    for (;!$result->EOF;$result->MoveNext()){
        $project_members[] = $result->fields[0];
    }

    $result->Close();

    return $project_members;
}

/**
 * get a specific group members
 * @param $args['group_id'] id of group to get
 * @returns array
 * @return item array, or false on failure
 */
function todolist_userapi_getgroupmembers($args)
{
    extract($args);

    if (!isset($group_id)) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_READ)) {
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_group_members_column = &$pntable['todolist_group_members_column'];
    $sql = "SELECT $todolist_group_members_column[member_id]
            FROM $pntable[todolist_group_members]
            WHERE $todolist_group_members_column[group_id]=".pnVarPrepForStore($group_id);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    $group_members = array();

    for (;!$result->EOF;$result->MoveNext()) {
        $group_members[] = $result->fields[0];
    }

    $result->Close();

    return $group_members;
}
?>