<?php
// 
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phxaruke.org/
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
// Original Author of file: Chad Kraeft
// Purpose of file:  xproject tasks API
// ----------------------------------------------------------------------

// ONLY ODD FUNCTION IS GETPROJECTMEMBERS

function xproject_tasksapi_getall($args)
{
    extract($args);
	
    if ($startnum == "") {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (!isset($filter)) {
        $filter = 0;
    }
	if(empty($parentid) || !is_numeric($parentid)) $parentid = 0;
	
    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'projectid';
    }
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'tasks', 'getall', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	
    $tasks = array();

    if (!xarSecAuthAction(0, 'xproject::Tasks', '::', ACCESS_OVERVIEW)) {
        $msg = xarML('Not authorized to access #(1) items',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];

    $sql = "SELECT xar_taskid,
                   xar_parentid,
                   xar_projectid,
                   xar_name,
				   xar_status,
				   xar_priority,
				   xar_description,
				   xar_private,
				   xar_creator,
				   xar_owner,
				   xar_assigner,
				   xar_groupid,
				   xar_date_created,
				   xar_date_approved,
				   xar_date_changed,
				   xar_date_start_planned,
				   xar_date_start_actual,
				   xar_date_end_planned,
				   xar_date_end_actual,
				   xar_hours_planned,
				   xar_hours_spent,
				   xar_hours_remaining,
				   xar_cost,
				   xar_recurring,
				   xar_periodicity,
				   xar_reminder
            FROM $taskstable";
			
// IMPLEMENT FILTER CODE FOR WHERE CLAUSE
$userId = xarSessionGetVar('uid');
switch($filter) {
	case 1: // My Tasks
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				AND (xar_creator = " . ($userId ? $userId : "0") . "
					OR xar_owner = " . ($userId ? $userId : "0") . "
					OR xar_assigner = " . ($userId ? $userId : "0") . ")
				AND xar_status = 0
				ORDER BY xar_priority DESC, xar_name";
		break;
	case 2: // Available Tasks
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				AND xar_owner IS NULL
				AND xar_status = 0
				ORDER BY xar_priority DESC, xar_name";
		break;
	case 3: // Priority List
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				AND xar_status = 0
				ORDER BY xar_priority DESC, xar_name";
		break;
	case 4: // Recent Activity
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				ORDER BY xar_status, xar_priority DESC, xar_name";
		break;
	case 5:
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				ORDER BY xar_status, xar_priority DESC, xar_name";
		break;
	case 0:
	default:
		$sql .= " WHERE xar_projectid = $projectid
				AND xar_parentid = " . ($parentid ? $parentid : "0") . "
				ORDER BY xar_status, xar_priority DESC, xar_name";

}			

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list($taskid,
			   $parentid,
			   $projectid,
			   $name,
			   $status,
			   $priority,
			   $description,
			   $private,
			   $creator,
			   $owner,
			   $assigner,
			   $groupid,
			   $date_created,
			   $date_approved,
			   $date_changed,
			   $date_start_planned,
			   $date_start_actual,
			   $date_end_planned,
			   $date_end_actual,
			   $hours_planned,
			   $hours_spent,
			   $hours_remaining,
			   $cost,
			   $recurring,
			   $periodicity,
			   $reminder) = $result->fields;
        if (xarSecAuthAction(0, 'xproject::Tasks', "::", ACCESS_READ)) {
			$numsubtasks = xarModAPIFunc('xproject', 'tasks', 'countitems', array('parentid' => $taskid));
            $tasks[] = array('taskid' => $taskid,
                             'parentid' => $parentid,
							 'projectid' => $projectid,
                             'name' => $name,
							 'status' => $status,
							 'priority' => $priority,
							 'description' => $description,
							 'private' => $private,
							 'creator' => $creator,
							 'owner' => $owner,
							 'assigner' => $assigner,
							 'groupid' => $groupid,
							 'date_created' => $date_created,
							 'date_approved' => $date_approved,
							 'date_changed' => $date_changed,
							 'date_start_planned' => $date_start_planned,
							 'date_start_actual' => $date_start_actual,
							 'date_end_planned' => $date_end_planned,
							 'date_end_actual' => $date_end_actual,
							 'hours_planned' => $hours_planned,
							 'hours_spent' => $hours_spent,
							 'hours_remaining' => $hours_remaining,
							 'cost' => $cost,
							 'recurring' => $recurring,
							 'periodicity' => $periodicity,
							 'reminder' => $reminder,
							 'numsubtasks' => $numsubtasks);
        }
    }

    $result->Close();

    return $tasks;
}

function xproject_tasksapi_get($args)
{
    extract($args);

    if (!isset($taskid) || !is_numeric($taskid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'tasks', 'get', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];

    $sql = "SELECT xar_taskid,
                   xar_parentid,
                   xar_projectid,
                   xar_name,
				   xar_status,
				   xar_priority,
				   xar_description,
				   xar_private,
				   xar_creator,
				   xar_owner,
				   xar_assigner,
				   xar_groupid,
				   xar_date_created,
				   xar_date_approved,
				   xar_date_changed,
				   xar_date_start_planned,
				   xar_date_start_actual,
				   xar_date_end_planned,
				   xar_date_end_actual,
				   xar_hours_planned,
				   xar_hours_spent,
				   xar_hours_remaining,
				   xar_cost,
				   xar_recurring,
				   xar_periodicity,
				   xar_reminder
			FROM $taskstable
            WHERE xar_taskid = " . $taskid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

	list($taskid,
		   $parentid,
		   $projectid,
		   $name,
		   $status,
		   $priority,
		   $description,
		   $private,
		   $creator,
		   $owner,
		   $assigner,
		   $groupid,
		   $date_created,
		   $date_approved,
		   $date_changed,
		   $date_start_planned,
		   $date_start_actual,
		   $date_end_planned,
		   $date_end_actual,
		   $hours_planned,
		   $hours_spent,
		   $hours_remaining,
		   $cost,
		   $recurring,
		   $periodicity,
		   $reminder) = $result->fields;
		
    $result->Close();

    if (!xarSecAuthAction(0, 'xproject::Tasks', "$name::$taskid", ACCESS_READ)) {
        $msg = xarML('Not authorized to access #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	
	$numsubtasks = xarModAPIFunc('xproject', 'tasks', 'countitems', array('parentid' => $taskid));
	
    $task = array('taskid' => $taskid,
				 'parentid' => $parentid,
				 'projectid' => $projectid,
				 'name' => $name,
				 'status' => $status,
				 'priority' => $priority,
				 'description' => $description,
				 'private' => $private,
				 'creator' => $creator,
				 'owner' => $owner,
				 'assigner' => $assigner,
				 'groupid' => $groupid,
				 'date_created' => $date_created,
				 'date_approved' => $date_approved,
				 'date_changed' => $date_changed,
				 'date_start_planned' => $date_start_planned,
				 'date_start_actual' => $date_start_actual,
				 'date_end_planned' => $date_end_planned,
				 'date_end_actual' => $date_end_actual,
				 'hours_planned' => $hours_planned,
				 'hours_spent' => $hours_spent,
				 'hours_remaining' => $hours_remaining,
				 'cost' => $cost,
				 'recurring' => $recurring,
				 'periodicity' => $periodicity,
				 'reminder' => $reminder,
				 'numsubtasks' => $numsubtasks);

    return $task;
}

function xproject_tasksapi_countitems($args)
{
	extract($args);
	
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject_tasks'];

	$sql = "SELECT COUNT(1)
			FROM $xprojecttable";
	if(isset($parentid)) {
		$sql .= " WHERE xar_parentid = $parentid";
	} elseif(isset($projectid)) {
		$sql .= " WHERE xar_projectid = $projectid";
	}
	
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    list($numtasks) = $result->fields;

    $result->Close();

    return $numtasks;
}

function makeSearchQuery($wildcards,$priority, $status, $project, $responsible_persons,$order_by,$date_min,$date_max)
{
    global $abfrage;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    /* Generate the SQL-Statement */
    $xproject_todos_column = &$xartable['xproject_todos_column'];
    $xproject_responsible_persons_column = &$xartable['xproject_responsible_persons_column'];
    $xproject_notes_column = &$xartable['xproject_notes_column'];

    $query="SELECT $xartable[xproject_todos].*, count($xproject_notes_column[todo_id]) AS nr_notes
        FROM $xartable[xproject_todos], $xartable[xproject_responsible_persons]
        LEFT JOIN $xartable[xproject_notes]
        ON $xproject_todos_column[todo_id]=$xproject_notes_column[todo_id]
        WHERE $xproject_todos_column[todo_text] LIKE ";

    if ($wildcards) {
        $query=$query . "'%$abfrage%' "; 
    } else {
        $query=$query . "'$abfrage' "; 
    }

    if ($priority!=""){
        $query=$query . "AND $xproject_todos_column[todo_priority]=$priority "; 
    }

    if ($status!="" && $status != "all"){
        $query=$query . "AND $xproject_todos_column[status]=$status "; 
    }

    if ($project!=""){
        if ($project != "all") {
            $query=$query . "AND $xproject_todos_column[project_id]=$project "; 
        } else {
            $xproject_project_members_column = &$xartable['xproject_project_members_column'];
            $sql2 = "SELECT $xproject_project_members_column[project_id]
               FROM $xartable[xproject_project_members]
               WHERE $xproject_project_members_column[member_id]=".
               xarUserGetVar('uid')."";
            $result = $dbconn->Execute($sql2);

            for (;!$result->EOF;$result->MoveNext()){
                $tasks[] = $result->fields[0];
            }
            if ($tasks[0]!="") {
                $query.=" AND $xproject_todos_column[project_id] in (";

                        while ($neu=array_pop($tasks)){
                        $query .= $neu;
                        if (sizeof($tasks) > 0)
                        $query .= ',';
                        else
                        $query .= ') ';
                        }
            }

        }
    }

    if ( ereg( "([0-9]{1,2})([.-/]{0,1})([0-9]{1,2})([.-/]{0,1})([0-9]{2,4})", trim($date_min), $regs ) ) {
        $date_min = mktime(0,0,0,$regs[1],$regs[2],$regs[0]);
    }
    if ( ereg( "([0-9]{1,2})([.-/]{0,1})([0-9]{1,2})([.-/]{0,1})([0-9]{2,4})", trim($date_max), $regs ) ) {
        $date_max = mktime(0,0,0,$regs[1],$regs[2],$regs[0]);
    }
    if (!$date_min){
        $date_min = "0";
    }

    if (!$date_max){
        $date_max = time();
    }

/*
    if (xarModGetVar('xproject', 'DATEFORMAT') != "1" ) {
        $date_min=convDateToUS($date_min);
        $date_max=convDateToUS($date_max);
    }
    if (!$date_min){ $date_min = "0000-00-00"; }
    if (!$date_max){ $date_max = date("Y-m-d");}
*/

    $query=$query . "AND $xproject_todos_column[date_changed] >= '$date_min'
    AND $xproject_todos_column[date_changed] <= '$date_max' ";

    /* sizeof(array) > 0 doesn't work? */
    if ($responsible_persons[0]!="") {
        $query.=" AND $xproject_responsible_persons_column[user_id] in (";

                while ($neu=array_pop($responsible_persons)){
                $query .= $neu;
                if (sizeof($responsible_persons) > 0)
                $query .= ',';
                else
                $query .= ') ';
                }
    }
    $query .= "AND $xproject_responsible_persons_column[todo_id]=$xproject_todos_column[todo_id]";

    $query=$query . " GROUP BY $xproject_todos_column[todo_id] ";

    // How should the table be ordered?
    $query .= orderBy($order_by);
    return $query;
}
// end makeSearchQuery

function xproject_tasksapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($projectid) || $projectid == 0) {
        $invalid[] = 'projectid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'tasks', 'create', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	
	// TODO: NEED TO WORK IN PROJECT NAME, CURRENTLY USING TASK NAME
    if (!xarSecAuthAction(0, 'xproject::Project', "$name::$projectid", ACCESS_ADD)) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
		
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];
	$xprojecttable = $xartable['xproject'];
    $nextId = $dbconn->GenId($xprojecttable);

    $sql = "INSERT INTO $taskstable (
               xar_taskid,
			   xar_parentid,
			   xar_projectid,
			   xar_name,
			   xar_status,
			   xar_priority,
			   xar_description,
			   xar_private,
			   xar_creator,
			   xar_owner,
			   xar_assigner,
			   xar_groupid,
			   xar_date_created,
			   xar_date_approved,
			   xar_date_changed,
			   xar_date_start_planned,
			   xar_date_start_actual,
			   xar_date_end_planned,
			   xar_date_end_actual,
			   xar_hours_planned,
			   xar_hours_spent,
			   xar_hours_remaining,
			   xar_cost,
			   xar_recurring,
			   xar_periodicity,
			   xar_reminder)
            VALUES (
              $nextId,
			  " . ($parentid ? $parentid : 0) . ",
			  $projectid,
              '" . xarVarPrepForStore($name) . "',
              " . xarVarPrepForStore($status) . ",
              " . xarVarPrepForStore($priority) . ",
              '" . xarVarPrepForStore($description) . "',
              " . ($private ? $private : "NULL") . ",
              " . xarSessionGetVar('uid') . ",
              " . xarSessionGetVar('uid') . ",
              " . xarSessionGetVar('uid') . ",
              NULL,
              " . time() . ",
			  NULL,
			  " . time() . ",
			  NULL,
			  NULL,
			  NULL,
			  NULL,
			  NULL,
			  NULL,
			  NULL,
			  0,
			  NULL,
			  NULL,
			  NULL)";			  

// PRIVATE INITIALLY SET BASED ON USER PREFERENCE

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $taskid = $dbconn->PO_Insert_ID($xprojecttable, 'xar_projectid');

    $item = $args;
    $item['module'] = 'xproject';
    $item['itemid'] = $taskid;
    xarModCallHooks('item', 'create', $taskid, $item);

    return $taskid;
}

function xproject_tasksapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($taskid) || $taskid == 0) {
        $invalid[] = 'taskid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'tasks', 'update', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $task = xarModAPIFunc('xproject',
						'tasks',
						'get',
						array('taskid' => $taskid));
			
	if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Project', "$task[name]::$taskid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to edit #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    if (!xarSecAuthAction(0, 'xproject::Project', "$name::$taskid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to edit #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
		
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];

    $sql = "UPDATE $taskstable
              SET xar_parentid = " . ($parentid ? $parentid : "0") . ",
			   xar_projectid = " . $projectid . ",
			   xar_name = '" . xarVarPrepForStore($name) . "',
			   xar_status = " . $status . ",
			   xar_priority = " . $priority . ",
			   xar_description = '" . xarVarPrepForStore($description) . "',
			   xar_date_changed = " . time() . "
			WHERE xar_taskid = $taskid";
/*
			   xar_date_start_planned = " . ($date_start_planned ? $date_start_planned : "NULL") . ",
			   xar_date_start_actual = " . ($date_start_actual ? $date_start_actual : "NULL") . ",
			   xar_date_end_planned = " . ($date_end_planned ? $date_end_planned : "NULL") . ",
			   xar_date_end_actual = " . ($date_end_actual ? $date_end_actual : "NULL") . ",
			   xar_hours_planned = " . ($hours_planned ? $hours_planned : "NULL") . ",
			   xar_hours_spent = " . ($hours_spent ? $hours_spent : "NULL") . ",
			   xar_hours_remaining = " . ($hours_remaining ? $hours_remaining : "NULL") . ",
			   xar_cost = " . ($cost ? $cost : "NULL") . ",
			   xar_recurring = " . ($recurring ? $recurring : "NULL") . ",
			   xar_periodicity = " . ($periodicity ? $periodicity : "NULL") . ",
			   xar_reminder = " . ($reminder ? $reminder : "NULL") . "
			WHERE xar_taskid = $taskid";
*/
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    $item['name'] = $name;
    $item['description'] = $description;
    xarModCallHooks('item', 'update', $projectid, $item);

    return true;
}

function xproject_tasksapi_delete($args)
{
    extract($args);

    if (!isset($taskid) || !is_numeric($taskid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'tasks', 'delete', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarModAPILoad('xproject', 'tasks')) return;

    // does it exist ?
    $task = xarModAPIFunc('xproject',
							'tasks',
							'get',
							array('taskid' => $taskid));

    if (!isset($task) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$taskid", ACCESS_DELETE)) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];

    // does it have children ?
    $sql = "DELETE FROM $taskstable
            WHERE xar_taskid = " . xarVarPrepForStore($taskid);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'xproject';
    $item['itemid'] = $taskid;
    xarModCallHooks('item', 'delete', $taskid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}
?>