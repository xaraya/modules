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
// Purpose of file:  task user display functions
// ----------------------------------------------------------------------

// PROJECT VIEW
function xproject_user_main()
{
    if (!xarSecAuthAction(0, 'xproject::', '::', ACCESS_OVERVIEW)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	
	$data = xproject_user_menu();
	$data['welcome'] = xarML('Welcome to the xproject module...');
	return $data;
}

function xproject_user_view($args)
{
    $startnum = xarVarCleanFromInput('startnum');

    $data = xproject_user_menu();

	$data['items'] = array();

    if (!xarSecAuthAction(0, 'xproject::', '::', ACCESS_OVERVIEW)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarModAPILoad('xproject', 'user')) return;

    $xprojects = xarModAPIFunc('xproject',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'numitems' => 10));

	if (!isset($xprojects) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;
	
    for ($i = 0; $i < count($xprojects); $i++) {
        $project = $xprojects[$i];
		if (xarSecAuthAction(0, 'xproject::Projects', "$project[name]::$project[projectid]", ACCESS_READ)) {
			$xprojects[$i]['link'] = xarModURL('xproject',
											   'user',
											   'display',
											   array('projectid' => $project['projectid']));
		}
		if (xarSecAuthAction(0, 'xproject::Projects', "$project[name]::$project[projectid]", ACCESS_EDIT)) {
			$xprojects[$i]['editurl'] = xarModURL('xproject',
											   'admin',
											   'modify',
											   array('projectid' => $project['projectid']));
		} else {
			$xprojects[$i]['editurl'] = '';
		}
		if (xarSecAuthAction(0, 'xproject::Projects', "$project[name]::$project[projectid]", ACCESS_DELETE)) {
			$xprojects[$i]['deleteurl'] = xarModURL('xproject',
											   'admin',
											   'delete',
											   array('projectid' => $project['projectid']));
		} else {
			$xprojects[$i]['deleteurl'] = '';
		}
    }

    $data['xprojects'] = $xprojects;
	$data['pager'] = '';
    return $data;
}

function xproject_user_display($args)
{
    list($projectid,
         $startnum,
         $taskid,
		 $filter,
         $objectid) = xarVarCleanFromInput('projectid',
                                          'startnum',
                                          'taskid',
										  'filter',
                                          'objectid');

    extract($args);
    if (!empty($objectid)) {
        $projectid = $objectid;
    }

	$data = xproject_user_menu();
	
	$data['status'] = '';
	$data['taskid'] = $taskid;
	
    if (!xarModAPILoad('xproject', 'user')) return;

    $project = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $projectid));

    if (!isset($project) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    list($project['name']) = xarModCallHooks('item',
                                         'transform',
                                         $project['projectid'],
                                         array($project['name']));

    $data['name'] = xarVarCensor($project['name']);
    $data['description'] = $project['description'];

	if(is_numeric($taskid) && $taskid > 0) {
		if (!xarModAPILoad('xproject', 'tasks')) return;
	
		$task = xarModAPIFunc('xproject',
							  'tasks',
							  'get',
							  array('taskid' => $taskid));
	
		if (isset($task) && xarExceptionMajor() == XAR_NO_EXCEPTION) {
			list($task['name']) = xarModCallHooks('item',
												 'transform',
												 $taskid,
												 array($task['name']));
		}
		
		$data['taskname'] = xarVarCensor($task['name']);
		$data['taskdescription'] = $task['description'];
		
		if (xarSecAuthAction(0, 'xproject::Tasks', '$task[name]::$taskid', ACCESS_EDIT)) {
			$data['curtask_editurl'] = xarModURL('xproject', 'tasks', 'modify', array('taskid' => $taskid));
		} else {
			$data['curtask_editurl'] = "";
		}
		if (xarSecAuthAction(0, 'xproject::Tasks', '$task[name]::$taskid', ACCESS_DELETE)) {
			$data['curtask_deleteurl'] = xarModURL('xproject', 'tasks', 'delete', array('taskid' => $taskid));
		} else {
			$data['curtask_deleteurl'] = "";
		}
		
		if($task['parentid'] > 0) {
			$parent = xarModAPIFunc('xproject',
								  'tasks',
								  'get',
								  array('taskid' => $task['parentid']));
		} else {
			$data['roottask'] = xarMLByKey('project overview');
		}
		
		if (isset($parent) && xarExceptionMajor() == XAR_NO_EXCEPTION) {
			$data['taskparent_name'] = $parent['name'];
			$data['taskparent_id'] = $parent['taskid'];
		}
		$data['taskroot_name'] = xarMLByKey('Project Top');
	}

    $data['hookoutput'] = xarModCallHooks('item',
                                         'display',
                                         $projectid,
                                         xarModURL('xproject',
                                                  'user',
                                                  'display',
                                                  array('projectid' => $projectid)));

	// BUILD TASK ADD FORM
    if (xarSecAuthAction(0, 'xproject::Projects', '$project[name]::$project[projectid]', ACCESS_MODERATE)) {
		$data['authid'] = xarSecGenAuthKey();

		$data['projectid'] = $project['projectid'];
		$data['parentid'] = $taskid;
	
		if(!isset($taskid) || $taskid == 0) $data['tasknamelabel'] = xarVarPrepForDisplay(xarMLByKey('New Task'));
		else $data['tasknamelabel'] = xarVarPrepForDisplay(xarMLByKey('New Sub-Task'));
		
		$statusoptions = array();    
		$statusoptions[] = array('id'=>0,'name'=>xarMLByKey('Open'));
		$statusoptions[] = array('id'=>1,'name'=>xarMLByKey('Closed'));
		$data['statusoptions'] = $statusoptions;

		$data['prioritydropdown'] = array();
		for($x=0;$x<=9;$x++) {
			$data['prioritydropdown'][] = array('id' => $x, 'name' => $x);
		}

		$data['addbutton'] = xarVarPrepForDisplay(xarMLByKey('Add'));
	
		$item = array();
		$item['module'] = 'xproject';
		$hooks = xarModCallHooks('item','new','',$item);
		if (empty($hooks) || !is_string($hooks)) {
			$data['hooks'] = '';
		} else {
			$data['hooks'] = $hooks;
		}
	}
	
	$filteroptions = array(xarMLByKey('default'),
							xarMLByKey('My Tasks'),
							xarMLByKey('Available Tasks'),
							xarMLByKey('Priority List'),
							xarMLByKey('Recent Activity'),
							"");
	$data['filteroptions'] = array();
	foreach($filteroptions as $id=>$name) {
		$data['filteroptions'][] = array('id' => $id,
										'name' => $name,
										'selected' => ($id == $filter ? 1 : 0));
	}
	$data['filterbutton'] = xarVarPrepForDisplay(xarMLByKey('Filter'));
	// BUILD TASKS ARRAY
	$data['tasks'] = array();
	if (xarModAPILoad('xproject', 'tasks')) {
		$data['tasklistfilter'] = $filter;
		$tasks = xarModAPIFunc('xproject',
								'tasks',
								'getall',
								array('startnum' => $startnum,
                                	'projectid' => $projectid,
									'parentid' => $taskid,
									'filter' => $filter));
		if (isset($tasks) && is_array($tasks) && (xarExceptionMajor() == XAR_NO_EXCEPTION)) {
			for ($i = 0; $i < count($tasks); $i++) {
				$task = $tasks[$i];
				$tasks[$i]['created'] = strftime($data['dateformatlist'][xarModGetVar('xproject','dateformat')],$task['date_created']);
				$tasks[$i]['modified'] = strftime($data['dateformatlist'][xarModGetVar('xproject','dateformat')],$task['date_changed']);
				$tasks[$i]['closed'] = ($task['status'] == 1) ? "x" : "";
				$tasks[$i]['createdby'] = xarUserGetVar('uname',$task['creator']);
				$tasks[$i]['assignedto'] = xarUserGetVar('uname',$task['owner']);
				if (xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$task[taskid]", ACCESS_EDIT)) {
					$tasks[$i]['editurl'] = xarModURL('xproject',
													   'tasks',
													   'modify',
													   array('taskid' => $task['taskid']));
				} else {
					$tasks[$i]['editurl'] = '';
				}
				if (xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$task[taskid]", ACCESS_DELETE)) {
					$tasks[$i]['deleteurl'] = xarModURL('xproject',
													   'tasks',
													   'delete',
													   array('taskid' => $task['taskid']));
				} else {
					$tasks[$i]['deleteurl'] = '';
				}
			}
			$data['tasks'] = $tasks;
			$data['numtasks'] = count($data['tasks']);
			$numtasks = count($data['tasks']);
		}
		
		$taskoptionslist = array(1 => xarMLByKey('Surface'),
								2 => xarMLByKey('Delete') . ' (' . xarMLByKey('delete subtasks') . ')',
								3 => xarMLByKey('Delete') . ' (' . xarMLByKey('move subtasks up') . ')');
		$taskoptions = array();
		foreach($taskoptionslist as $optionid=>$option) {
			$taskoptions[] = array('id' => $optionid,
									'name' => $option);
		}
		$data['taskoptions'] = $taskoptions;
	}

	return $data;
}

function xproject_user_menu()
{
    $menu = array();

	$dateformatlist = array('Please choose a Date/Time Format',
							'%m/%d/%Y',
							'%B %d, %Y',
							'%a, %B %d, %Y',
							'%A, %B %d, %Y',
							'%m/%d/%Y %H:%M',
							'%B %d, %Y %H:%M',
							'%a, %B %d, %Y %H:%M',
							'%A, %B %d, %Y %H:%M',
							'%m/%d/%Y %I:%M %p',
							'%B %d, %Y %I:%M %p',
							'%a, %B %d, %Y %I:%M %p',
							'%A, %B %d, %Y %I:%M %p');
	$menu['dateformatlist'] = $dateformatlist;

    $menu['menutitle'] = xarModGetVar('xproject','todoheading');

    $menu['menulabel_view'] = xarMLByKey('Projects');
    $menu['menulabel_new'] = xarMLByKey('New Project');
    $menu['menulabel_search'] = xarMLByKey('Search');
    $menu['menulabel_config'] = xarMLByKey('Config');
    
    return $menu;
}
?>