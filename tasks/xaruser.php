<?php
error_reporting(2039);

// TASK VIEW
function tasks_user_main()
{
	xarResponseRedirect(xarModURL('tasks','user','view'));
	return true;
}

function tasks_user_view($args)
{
	list($parentid,
		$module,
		$type,
		$func,
		$filter,
		$displaydepth) = pnVarCleanFromInput('parentid',
									'module',
									'type',
									'func',
									'filter',
									'displaydepth');
	
	extract($args);

    $output = new pnHTML();

	$output->SetInputMode(_PNH_VERBATIMINPUT);

	if($module == "tasks"
      && ($type == "user" || $type == "")
      && ($func == "view" || $func == "")) {
		$output->Text(tasks_menu());
	}

	$maxlevel = pnSessionGetVar('maxlevel');
	if(!isset($displaydepth)) {
		$displaydepth = ($maxlevel ? $maxlevel : 1);
	}
	pnSessionSetVar('maxlevel', $displaydepth);

    if (!pnModAPILoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        $output->Text(tasks_feedback());
		return $output->GetOutput();
    }

    $tasks = pnModAPIFunc('tasks',
                          'user',
                          'getall',
						  array('parentid' => $parentid,
						  		'modname' => $module,
						  		'objectid' => $objectid,
						  		'displaydepth' => 1));

	if ($tasks == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_ITEMFAILED);
    }
    
	$basetaskid = pnModAPIFunc('tasks', 'user', 'getroot', array('id' => $parentid));
  
	if($module == "tasks" && ($type == "user" || $type == "") && $func == "view") {
		$output->Text(tasks_feedback());
	}

	$output->FormStart(pnModURL('tasks',
								'user',
								($parentid ? 'display' : 'view'),
								array('' => '#tasklist')));

    $output->FormHidden('id', $parentid);
    $output->FormHidden('modname', $modname);
    $output->FormHidden('objectid', $objectid);

	$filters = array(_TASKS_FILTER_DEFAULT,
					_TASKS_FILTER_MYTASKS,
					_TASKS_FILTER_AVAILABLETASKS,
					_TASKS_FILTER_PRIORITYLIST,
					_TASKS_FILTER_RECENTACTIVITY);
	$filteroptions = array();
	$filter = pnSessionGetVar('filter');
	foreach($filters as $filterid=>$filtername) {
		$filteroptions[] = array('id' => $filterid,
								'name' => $filtername,
								'selected' => ($filterid == $filter ? 1 : 0));
	}
	$depthdropdown = array();
	$maxdepth = pnModGetVar('tasks', 'maxdisplaydepth');
	for($x=1; $x<=$maxdepth; $x++) {
		$depthdropdown[] = array('id'=>$x, 'name'=>$x);
	}
	$output->FormSelectMultiple('displaydepth', $depthdropdown, 0, 1, pnSessionGetVar('maxlevel'));
	$output->FormSelectMultiple('filter', $filteroptions, 0, 1);
	$output->FormSubmit(_TASKS_FILTER);
	if($filter == 1 || $filter == 2 || $filter == 3) {
		$output->Text(_TASKS_OPENTASKSONLY);
	}
	$output->FormEnd();

	$output->FormStart(pnModURL('tasks', 'admin', 'migrate'));

    $output->FormHidden('parentid', $parentid);
    $output->FormHidden('modname', $modname);
    $output->FormHidden('objectid', $objectid);

	$output->TableStart(_TASKS_TASKLIST,
						array(_TASKS_TASKS,
								_TASKSDESCRIPTION,
								_TASKS_PRIVATE,
								_TASKS_PRIORITY,
								_TASKS_STATUS,
								_TASKS_SUBTASKS,
								_TASKS_OPTIONS),
						1,
						"80%");
								
	if(is_array($tasks) && count($tasks) > 0) {
		foreach($tasks as $task) {
			$dateformat = pnModGetVar('tasks', 'dateformat');
			$dateformatlist = tasks_dateformatlist();
			if(empty($dateformat)) $dateformat = 1;
			$task['date_created'] = strftime($dateformatlist[$dateformat],$task['date_created']);
			$task['date_changed'] = strftime($dateformatlist[$dateformat],$task['date_changed']);
			$task['private'] = ($task['private'] == 1) ? "?" : "";
			$task['closed'] = ($task['status'] == 1) ? "x" : "";
			$task['creator'] = pnUserGetVar('uname',$task['creator']);
			$task['owner'] = pnUserGetVar('uname',$task['owner']);
					
			switch($task['depth']) {
				case 4:
					$bgcolor = "EEEEEE";
					break;
				case 3:
					$bgcolor = "EEEEFF";
					break;
				case 2:
					$bgcolor = "CCCCFF";
					break;
				case 1:
				default:
					$bgcolor = "AAAAFF";
			}
			$indent = "";
			for($x=2;$x<=$task['depth'];$x++) {
				$indent .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			$output->Text('<tr bgcolor="' . $bgcolor . '" valign="middle"><td align="left"width="100%" colspan="2"><b>' . $indent);
			$output->URL(pnModURL('tasks',
								   'user',
								   'display',
								   array('id' => $task['id'])),
							$task['name']);
			$output->Text('</b></td>');
			$output->Text('<td align="center">' . $task['private'] . '</td>');
			$output->Text('<td align="center">' . $task['priority'] . '</td>');
			$output->Text('<td align="center">' . $task['closed'] . '</td>');
			$output->Text('<td align="center">' . $task['closedsubtasks'] . ' / ' . $task['ttlsubtasks'] . '</td>');
			$output->Text('<td align="center">');
			if (pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_ADMIN)
					|| (pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid:$task[basetaskid]', ACCESS_MODERATE)
						&& ($task['creator'] == $userID
							|| $task['owner'] == $userID
							|| $task['assigner'] == $userID))) {
				$output->FormCheckBox('taskcheck[' . $task['id'] . ']');
				$output->Text('<input type="submit" name="taskfocus[' . $task['id'] . ']" value=" + ">');
			}
			$output->Text('</td></tr>');

			$output->Text('<tr bgcolor="' . $bgcolor . '"><td align="left" valign="top" width="30%">');
			if($task['depth'] == 1) {
				$output->Text($indent . '[created: ' . $task['date_created'] . ']');
				$output->Linebreak();
				$output->Text($indent . '[modified: ' . $task['date_changed'] . ']');
				$output->Linebreak();
			}
			if($task['depth'] <= 2) {
				$output->Text($indent . '[creator: ' . $task['creator'] . ']');
				$output->Linebreak();
				$output->Text($indent . '[assigned to: ' . $task['owner'] . ']');
			}
			$output->Text('&nbsp;</td>');
			$output->Text('<td align="left" valign="top" width="100%" colspan="6">' . $task['description']);
			if (pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE)
					|| (pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE)
						&& ($task['creator'] == $userID
							|| $task['owner'] == $userID
							|| $task['assigner'] == $userID))) {
				if(pnModGetVar('tasks', 'showoptions')) {
					if(!empty($task['description'])) $output->Linebreak();
					$output->Text('[ <i>');
					$options = array();
					$output->SetOutputMode(_PNH_RETURNOUTPUT);
					$options[] = $output->URL(pnModURL('tasks',
												   'admin',
												   'modify',
												   array('id' => $task['id'])),
											_TASKS_EDIT);
		// ONLY ALLOW ACCEPT IF CURRENTLY UNASSIGNED
		// IF ASSIGNED TO CURRENT USER, OR AFTER ACCEPTED, MUST BE APPROVED BEFORE RE-ASSIGNMENT (see below)
					$options[] = $output->URL(pnModURL('tasks',
												   'admin',
												   'accept',
												   array('id' => $task['id'])),
											_TASKS_TASKACCEPT);
											
		// THIS FORCES APPROVAL BEFORE RE-ASSIGNMENT
		// NEED TO IMPLEMENT USER LIST FOR RE-ASSIGNMENT
		// PULL ALL MEMBERS OF GROUPS *OTHER* THAN PRIMARY USER GROUP *UNLESS* CURRENT
		//   USER IS NOT A MEMBER OF ANY OTHER GROUP
		//   CREATE NEW FUNCTION TO HANDLE THIS: tasks_userapi_getpeers($args('uid' = pnSessionGetVar('uid')))
	
					if($task['date_approved'] > 0) {
						$options[] = $output->URL(pnModURL('tasks',
													   'admin',
													   'assign',
													   array('id' => $task['id'])),
												_TASKS_ASSIGN);
					} else {
						$options[] = $output->URL(pnModURL('tasks',
													   'admin',
													   'approve',
													   array('id' => $task['id'])),
												_TASKS_APPROVE);
					}
					if($task['status'] == 1) {
						$options[] = $output->URL(pnModURL('tasks',
													   'admin',
													   'open',
													   array('id' => $task['id'])),
												_TASKS_OPEN);
					} else {
						$options[] = $output->URL(pnModURL('tasks',
													   'admin',
													   'close',
													   array('id' => $task['id'])),
												_TASKS_CLOSE);
					}
					if(empty($task['private'])) {
						$options[] = $output->URL(pnModURL('tasks',
													   'admin',
													   'publish',
													   array('id' => $task['id'])),
												_TASKS_UNPUBLISH);
					} else {
						$options[] = $output->URL(pnModURL('tasks',
													   'admin',
													   'publish',
													   array('id' => $task['id'])),
												_TASKS_PUBLISH);
					}
					$output->SetOutputMode(_PNH_KEEPOUTPUT);
					$output->Text(implode(" | ", $options));
					$output->Text('</i> ]');
				}
			}
			$output->Text('</td></tr>');
		}
	}

	$taskoptionslist = array(1 => _TASKS_SURFACE,
							2 => _TASKS_DELETE . ' (' . _TASKS_DELSUBS . ')',
							3 => _TASKS_DELETE . ' (' . _TASKS_SURFACESUBS . ')');
	$taskoptions = array();
	foreach($taskoptionslist as $optionid=>$option) {
		$taskoptions[] = array('id' => $optionid,
								'name' => $option);
	}

	$output->Text('<tr><td colspan="6" align="right">' . _TASKS_SELECTEDTASKS);
	$output->FormSelectMultiple('taskoption', $taskoptions, 0, 1);
	$output->FormSubmit(_TASKS_TASKSUBMIT);
	$output->Text('</td></tr>');

	$output->TableEnd();

	$output->FormEnd();

    return $output->GetOutput();
}

function tasks_user_display($args)
{
    list($modname,
         $mainid,
		 $filter,
         $objectid,
         $module,
		$type,
		$func) = pnVarCleanFromInput('modname',
									'id',
									'filter',
									'objectid',
									'module',
									'type',
									'func');

// HOW TO GET MODULE ID OF CALLING MODULE?
// CHECK IF OUTPUT HAS ALREADY BEEN DISPLAYED
// IF NOT, USE PNVARCLEANFROMINPUT('MODULE') TO GET MOD ID
// THEN SET OUTPUT AS DISPLAYED
// WHERE TO UNSET?
// SET SESSION VAR FOR DISPLAYED AND FOR MODULE NAME
// IF MODULE NAME IS DIFFERENT, UNSET DISPLAYED

    extract($args);

	if(empty($mainid) || !is_numeric($mainid)) {
		pnSessionSetVar('errormgs', pnGetStatusMsg() . '<br>' . _TASKS_MODARGSERROR . ': tasks_user_display');
		pnRedirect(pnModURL('tasks', 'user', 'view'));
		return;
	} elseif(empty($id)) {
		$id = $mainid;
	}

	if(!isset($id)) {
		pnSessionSetVar('errormgs', pnGetStatusMsg() . '<br>' . _TASKS_MODARGSERROR . ': tasks_user_display');
		pnRedirect(pnModURL('tasks', 'user', 'view'));
		return;
	}

	$output = new pnHTML();

    $output->SetInputMode(_PNH_VERBATIMINPUT);
	if($module == "tasks" && ($type == "user" || $type == "") && $func == "display" && $id == $mainid) {
	    $output->Text(tasks_menu());
	}

    if (!pnModAPILoad('tasks', 'user')
			|| !pnModLoad('tasks', 'admin')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
		$output->Text(tasks_feedback());
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('tasks',
                          'user',
                          'get',
                          array('id' => $id));

    if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_ITEMFAILED);
		// WHAT DOES THIS DO?
		if($id == $mainid) {
			$output->Text(tasks_feedback());
			return $output->GetOutput();
		}
    }

	$userID = pnSessionGetVar('uid');
	// IS CLIENT OR MEMBER AND TASK IS PUBLIC
	// IS CREATOR/OWNER/ASSIGNER OF PRIVATE TASK?
	// IS MODERATOR / PM
	if ((!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_READ) && $task['private'] == 0)
			&& (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_COMMENT) && ($task['creator'] == $userID || $task['owner'] == $userID || $task['assigner'] == $userID))
			&& (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE))) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
		$output->Text(tasks_feedback());
        return $output->GetOutput();
    }

	if($id == $mainid) {
		if($task['parentid'] > 0) { // IF NOT ROOT
			$parent = pnModAPIFunc('tasks', 'user', 'get', array('id' => $task['parentid']));
			if($task['basetaskid'] == false) {
				pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_INVALIDBASETASK);
			} else {
				$displaybase = pnModFunc('tasks', 'user', 'display', array('id' => $task['basetaskid']));
			}
		}
	
		if($task['ttlsubtasks'] > 0) {
			$subtaskview = pnModFunc('tasks',
									'user',
									'view',
									array('parentid' => $id,
										'filter' => $filter));
		}
	
		$addtaskform = pnModFunc('tasks', 'admin', 'new', array('parentid' => $id));

		$output->Text(tasks_feedback());

	}

	if($task['parentid'] > 0) {
		$output->Text('<a name="task">(<a href="#top">top</a>)');
		$output->Text($displaybase);
		$output->Title(_TASKS_TASKDISPLAY);
	} else {
		$output->Title(_TASKS_ROOTTASKDISPLAY);
	}

    $output->Text(_TASKS_TASKNAME . ': ');
    $output->BoldText(pnVarPrepForDisplay(pnVarCensor($task['name'])));
	if($task['parentid'] > 0) {
		$output->Linebreak();
		$output->Text(_TASKS_TASKPARENT . ': ');
		if(!empty($parent['name'])) {
			$output->URL(pnModURL('tasks',
								'user',
								'display',
								array('id' => $task['parentid'])),
							pnVarPrepForDisplay($parent['name']));
		} else {
			$output->URL(pnModURL('tasks',
								'user',
								'view'),
							_TASKS_OVERVIEW);
		}
	}
    $output->Linebreak();
    $output->Text(_TASKS_TASKDESCRIPTION . ': ');
    $output->Text(pnVarPrepHTMLDisplay(pnVarCensor($task['description'])));
    $output->Linebreak(2);

	$options = array();
	$output->SetOutputMode(_PNH_RETURNOUTPUT);
	$options[] = $output->URL(pnModURL('tasks',
								   'admin',
								   'modify',
								   array('id' => $task['id'])),
							_TASKS_EDIT);
// ONLY ALLOW ACCEPT IF CURRENTLY UNASSIGNED
// IF ASSIGNED TO CURRENT USER, OR AFTER ACCEPTED, MUST BE APPROVED BEFORE RE-ASSIGNMENT (see below)
	$options[] = $output->URL(pnModURL('tasks',
								   'admin',
								   'accept',
								   array('id' => $task['id'])),
							_TASKS_TASKACCEPT);
							
// THIS FORCES APPROVAL BEFORE RE-ASSIGNMENT
// NEED TO IMPLEMENT USER LIST FOR RE-ASSIGNMENT
// PULL ALL MEMBERS OF GROUPS *OTHER* THAN PRIMARY USER GROUP *UNLESS* CURRENT
//   USER IS NOT A MEMBER OF ANY OTHER GROUP
//   CREATE NEW FUNCTION TO HANDLE THIS: tasks_userapi_getpeers($args('uid' = pnSessionGetVar('uid')))

	if($task['date_approved'] > 0) {
		$options[] = $output->URL(pnModURL('tasks',
									   'admin',
									   'assign',
									   array('id' => $task['id'])),
								_TASKS_ASSIGN);
	} else {
		$options[] = $output->URL(pnModURL('tasks',
									   'admin',
									   'approve',
									   array('id' => $task['id'])),
								_TASKS_APPROVE);
	}
	if($task['status'] == 1) {
		$options[] = $output->URL(pnModURL('tasks',
									   'admin',
									   'open',
									   array('id' => $task['id'])),
								_TASKS_OPEN);
	} else {
		$options[] = $output->URL(pnModURL('tasks',
									   'admin',
									   'close',
									   array('id' => $task['id'])),
								_TASKS_CLOSE);
	}
	if(empty($task['private'])) {
		$options[] = $output->URL(pnModURL('tasks',
									   'admin',
									   'publish',
									   array('id' => $task['id'])),
								_TASKS_UNPUBLISH);
	} else {
		$options[] = $output->URL(pnModURL('tasks',
									   'admin',
									   'publish',
									   array('id' => $task['id'])),
								_TASKS_PUBLISH);
	}
	$output->SetOutputMode(_PNH_KEEPOUTPUT);
	$output->Text('<center>' . implode(" | ", $options) . '</center>');

	$output->Linebreak(2);

	if($id == $mainid) {
		$output->Text('<a name="addtask">(<a href="#top">top</a>)');
		$output->Text($addtaskform);
	
		$output->Text('<a name="tasklist">(<a href="#top">top</a>)');
		$output->Text($subtaskview);
	}

	return $output->GetOutput();
}

function tasks_menu()
{
	$output = new pnHTML();

	list($filter,$id,$module,$type,	$func) = pnVarCleanFromInput('filter',
									'id',
									'module',
									'type',
									'func');
									
	if(isset($filter)) pnSessionSetVar('filter', $filter);

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableStart(_TASKS);
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $columns = array();
	if(isset($id) && $module == "tasks" && $func == "display") {
		$columns[] = $output->Text('<a href="#task">Task</a>');	
		$columns[] = $output->Text('<a href="#addtask">New Subtask</a>');	
		$columns[] = $output->Text('<a href="#tasklist">Subtask List</a>');	
	}
    $columns[] = $output->URL(pnModURL('tasks',
                                       'user',
                                       'view'),
                              _TASKS_VIEW);
	if (pnSecAuthAction(0, 'tasks::task', '::', ACCESS_ADD)) {
		$columns[] = $output->URL(pnModURL('tasks',
										   'admin',
										   'new'),
								  _TASKS_ADD);
	}
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->Text('<tr><td align=center>' . implode(" | ",$columns) . '</td></tr>');
    $output->TableEnd();

    return $output->GetOutput();
}

function tasks_feedback()
{
	$feedback = xarGetStatusMsg();
    if(empty($feedback)) $feedback="";
	return $feedback;
}

function tasks_dateformatlist() {
	$dateformatlist = array(xarML('Please choose a Date/Time Format'),
							'%m/%d/%Y',
							'%m.%d.%y',
							'%B %d, %Y',
							'%a, %B %d, %Y',
							'%A, %B %d, %Y',
							'%m/%d/%Y %H:%M',
							'%m.%d.%y %H:%M',
							'%B %d, %Y %H:%M',
							'%a, %B %d, %Y %H:%M',
							'%A, %B %d, %Y %H:%M',
							'%m/%d/%Y %I:%M %p',
							'%m.%d.%y %I:%M %p',
							'%B %d, %Y %I:%M %p',
							'%a, %B %d, %Y %I:%M %p',
							'%A, %B %d, %Y %I:%M %p');
	return $dateformatlist;
}
?>