<?php

/**
 * File: $Id$
 *
 * Administration gui for tasks
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * 
 * @subpackage tasks
 * @author Chad Kraeft
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Administration entry point
 *
 */
function tasks_admin_main()
{
    $data=array();
    $data['welcome']=xarML('Welcome to the administration part of tasks module...');
    $data['pageinfo']=xarML('Overview');
	return $data;
}

/**
 * View tasklist
 *
 */
function tasks_admin_view()
{
    xarResponseRedirect(xarModURL('tasks','user','view'));
	return true;
}

/**
 * Add a new task
 *
 */
function tasks_admin_new($args)
{
    $data=array();
	list($module,$type,	$func) = xarVarCleanFromInput('module',
									'type',
									'func');

	extract($args);
	
	if (!xarModLoad('tasks', 'user')) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        xarResponseRedirect(xarModURL('tasks','user','view'));
		return;
    }
	
  // DISPLAY ONLY IF COMMENT AUTH FOR BASETASKID, OR MOD AUTH FOR NO BASETASKID
//     if (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_ADD)) {
//         pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
//         pnRedirect(pnModURL('tasks','user','view'));
// 		return;
//     }

// NEED TO INVESTIGATEN THIS
// 	if($module == "tasks" && $type == "admin" && $func == "new") {
// 	    $output->Text(tasks_menu());
// 	}

	$statusoptions = array();    
	$statusoptions[] = array('id'=>0,'name'=>xarML('Open'));
	$statusoptions[] = array('id'=>1,'name'=>xarML('Closed'));
    $data['statusoptions']=$statusoptions;

	$prioritydropdown = array();
	for($x=0;$x<=9;$x++) {
		$prioritydropdown[] = array('id' => $x, 'name' => $x);
	}
    $data['prioritydropdown']=$prioritydropdown;
	
    // NEED TO INVESTIGATE THIS
// 	if($module == "tasks" && $type == "admin" && $func == "new") {
// 		$output->Text(tasks_feedback());
// 	}
    //$data['feedback']=tasks_feedback();

    $data['parentid']= $parentid;
    $data['modname']= $modname;
    $data['objectid']= $objectid;

    $data['submitbutton']=xarML('Add task');

    return $data;
/*
// EXTRANEOUS	
    $sendmailoptions = array();    
    $sendmailoptions[] = array('id'=>0,'name'=>'Please choose an email option');
    $sendmailoptions[] = array('id'=>1,'name'=>"any changes");
    $sendmailoptions[] = array('id'=>2,'name'=>"major changes");
    $sendmailoptions[] = array('id'=>3,'name'=>"weekly summaries");
    $sendmailoptions[] = array('id'=>4,'name'=>"Do NOT send email");
	$data['sendmailoptions'] = $sendmailoptions;
    $data['sendmails'] = pnVarPrepForDisplay(pnMLByKey('Email Group'));
	for($x=0;$x<=9;$x++) {
		$data['importantdaysdropdown'][] = array('id' => $x, 'name' => $x);
	}
    $data['importantdays'] = pnVarPrepForDisplay(pnMLByKey('Important Days'));
	for($x=0;$x<=9;$x++) {
		$data['criticaldaysdropdown'][] = array('id' => $x, 'name' => $x);
	}
*/
}

function tasks_admin_create($args)
{
	list($parentid,
		$modname,
		$objectid,
		$name,
		$description,
		$status,
		$priority) =	pnVarCleanFromInput('parentid',
											'modname',
											'objectid',
											'name',
											'description',
											'status',
											'priority');

    extract($args);

    if (!pnModAPILoad('tasks', 'admin')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_ADMINAPILOADFAILED);
        pnRedirect(pnModURL('tasks', 'user', 'view'));
        return true;
    }

	// SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
	// PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    $returnid = pnModAPIFunc('tasks',
						'admin',
						'create',
						array('parentid' 	=> $parentid,
							'modname' 		=> $modname,
							'objectid' 		=> $objectid,
							'name' 			=> $name,
							'status' 		=> $status,
							'priority' 		=> $priority,
							'description'	=> $description));

    if ($returnid != false) {
        // Success
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_TASKCREATED);
    }

	pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#addtask')));

    return true;
}

function tasks_admin_modify($args)
{
	$id = pnVarCleanFromInput('id');
                           
    extract($args);

    $output = new pnHTML();

    if (!pnModAPILoad('tasks', 'user')
			||!pnModLoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
	if($module == "tasks" && $type == "admin" && $func == "modify") {
	    $output->Text(tasks_menu());
	}

    $task = pnModAPIFunc('tasks',
                         'user',
                         'get',
                         array('id' => $id));

    if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOSUCHITEM);
        $output->Text(tasks_feedback());
		return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
        $output->Text(tasks_feedback());
		return $output->GetOutput();
    }

	$statusoptions = array();    
	$statusoptions[] = array('id'=>0,'name'=>'Open');
	$statusoptions[] = array('id'=>1,'name'=>'Closed');

	$prioritydropdown = array();
	for($x=0;$x<=9;$x++) {
		$prioritydropdown[] = array('id' => $x, 'name' => $x);
	}

    if($module == "tasks" && $type == "admin" && $func == "create") {
		$output->Text(tasks_feedback());
	}

    $output->FormStart(pnModURL('tasks', 'admin', 'update'));

    $output->FormHidden('id', $id);

    $output->TableStart(_TASKS_EDITTASK, array(), 0, 550);

    $output->Text('<tr><td>');
    $output->Text(_TASKS_TASKNAME);
    $output->Text('</td><td colspan=2>');
    $output->FormText('name', $task['name'], 50, 255);
//    $row[] = $output->Text(_TASKS_TASKSTATUS);
    $output->Text('</td><td>');
    $output->FormSelectMultiple('status', $statusoptions, 0, 1, $task['status']);
    $output->Text('</td></tr>');
	
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(_TASKS_TASKPRIVATE);
    $row[] = $output->FormCheckbox('private', $task['private']);
    $row[] = $output->Text(_TASKS_TASKPRIORITY);
    $row[] = $output->FormSelectMultiple('priority', $prioritydropdown, 0, 1, $task['priority']);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'left');
	
	$dateformatlist = tasks_dateformatlist();
	$dateformat = $dateformatlist[pnModGetVar('tasks', 'dateformat')];
	$formsize = strlen($dateformat) * 2;
	$oneday = 60 * 60 * 24;
	$onemonth = $oneday * 30;
	$rangestart = time() - $onemonth;
	$rangeend = time() + $onemonth;
	$datedropdown = array();
	for($x = $rangestart; $x <= $rangeend;) {
		$datedropdown[] = array('id' => date("Ymd",$x),
								'name' => strftime($dateformat,$x));
		$x += $oneday;
	}

    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(_TASKS_TASKSTARTPLANNED);
    $row[] = $output->FormSelectMultiple('date_start_planned', $datedropdown, 0, 1, $task['date_start_planned']);
    $row[] = $output->Text(_TASKS_TASKSTARTACTUAL);
    $row[] = $output->FormSelectMultiple('date_start_actual', $datedropdown, 0, 1, $task['date_start_actual']);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'left');
	
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(_TASKS_TASKENDPLANNED);
    $row[] = $output->FormSelectMultiple('date_end_planned', $datedropdown, 0, 1, $task['date_end_planned']);
    $row[] = $output->Text(_TASKS_TASKENDACTUAL);
    $row[] = $output->FormSelectMultiple('date_end_actual', $datedropdown, 0, 1, $task['date_end_actual']);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'left');
	
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
	$row[] = $output->Text(_TASKS_TASKHRS);
	$row[] = $output->FormText('hours_planned', $task['hours_planned'], 6, 11)
    		.$output->Text(_TASKS_TASKHRSPLANNED);
	$row[] = $output->FormText('hours_spent', $task['hours_spent'], 6, 11)
    		.$output->Text(_TASKS_TASKHRSSPENT);
	$row[] = $output->FormText('hours_remaining', ($task['hours_remaining'] ? $task['hours_remaining'] : "0.00"), 6, 11)
    		.$output->Text(_TASKS_TASKHRSREMAINING);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'left');
	
    $output->Text('<tr><td>');
    $output->Text(_TASKS_TASKDESCRIPTION);
    $output->Text('</td><td colspan=3>');
    $output->FormTextarea('description', $task['description'], 8, 50 + $formsize);
    $output->Text('</td></tr>');

    $output->TableEnd();

    $output->FormSubmit(_TASKS_UPDATE);

	$output->FormEnd();

    return $output->GetOutput();
}

function tasks_admin_update($args)
{
	list($id,
        $name,
		$priority,
        $status,
        $description,
		$private,
        $owner,
        $assigner,
        $date_start_planned,
        $date_start_actual,
        $date_end_planned,
        $date_end_actual,
		$hours_planned,
		$hours_spent,
		$hours_remaining) = pnVarCleanFromInput('id',
										'name',
										'priority',
										'status',
										'description',
										'private',
										'owner',
										'assigner',
										'date_start_planned',
										'date_start_actual',
										'date_end_planned',
										'date_end_actual',
										'hours_planned',
										'hours_spent',
										'hours_remaining');

    extract($args);

	// SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
	// PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    if (!pnModAPILoad('tasks', 'admin')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $id)));
        return true;
    }

    if($returnid = pnModAPIFunc('tasks',
								'admin',
								'update',
								array('id'	=> $id,
									'name' 			=> $name,
									'status' 		=> $status,
									'priority' 		=> $priority,
									'description'	=> $description,
									'private' 		=> $private,
									'owner' 		=> $owner,
									'assigner' 		=> $assigner,
									'date_start_planned' 	=> $date_start_planned,
									'date_start_actual' 	=> $date_start_actual,
									'date_end_planned' 		=> $date_end_planned,
									'date_end_actual' 		=> $date_end_actual,
									'hours_planned' => $hours_planned,
									'hours_spent' 	=> $hours_spent,
									'hours_remaining' 		=> $hours_remaining))) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_UPDATED);
    }

    pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#tasklist')));

    return true;
}

function tasks_admin_close($args)
{
	$id = pnVarCleanFromInput('id');

    extract($args);

	// SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
	// PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    if (!pnModAPILoad('tasks', 'admin')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $id)));
        return true;
    }

    if($returnid = pnModAPIFunc('tasks',
								'admin',
								'close',
								array('id'	=> $id))) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_UPDATED);
    }

    pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#tasklist')));

    return true;
}

function tasks_admin_open($args)
{
	$id = pnVarCleanFromInput('id');

    extract($args);

	// SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
	// PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    if (!pnModAPILoad('tasks', 'admin')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $id)));
        return true;
    }

    if($returnid = pnModAPIFunc('tasks',
								'admin',
								'open',
								array('id'	=> $id))) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_UPDATED);
    }

    pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#tasklist')));

    return true;
}

function tasks_admin_approve($args)
{
	$id = pnVarCleanFromInput('id');

    extract($args);

	// SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
	// PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    if (!pnModAPILoad('tasks', 'admin')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $id)));
        return true;
    }

    if($returnid = pnModAPIFunc('tasks',
								'admin',
								'approve',
								array('id'	=> $id))) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_UPDATED);
    }

    pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#tasklist')));

    return true;
}

function tasks_admin_publish($args)
{
	$id = pnVarCleanFromInput('id');

    extract($args);

	// SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
	// PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    if (!pnModAPILoad('tasks', 'admin')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $id)));
        return true;
    }

    if($returnid = pnModAPIFunc('tasks',
								'admin',
								'publish',
								array('id'	=> $id))) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_UPDATED);
    }

    pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#tasklist')));

    return true;
}

function tasks_admin_accept($args)
{
	$id = pnVarCleanFromInput('id');

    extract($args);

	// SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
	// PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    if (!pnModAPILoad('tasks', 'admin')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $id)));
        return true;
    }

    if($returnid = pnModAPIFunc('tasks',
								'admin',
								'accept',
								array('id'	=> $id))) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_UPDATED);
    }

    pnRedirect(pnModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#tasklist')));

    return true;
}

// DEPRECATED
/*
function tasks_admin_delete($args)
{
    list($id,
         $confirmation) = pnVarCleanFromInput('id',
										  'confirmation');

    extract($args);

	if (!pnModAPILoad('tasks', 'user')
			|| !pnModLoad('tasks','user')
			|| !pnModAPILoad('tasks', 'admin')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        return true;
    }

    $task = pnModAPIFunc('tasks',
							 'user',
							 'get',
							 array('id' => $id));

    if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOSUCHITEM);
        return true;
    }

    if (!pnSecAuthAction(0, 'tasks::task', '::$task[basetaskid]', ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
        return true;
    }

    if (empty($confirmation)) {
        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(tasks_menu());

        $output->Title(_TASKS_DELETETASK);

		$output->Text(_TASKS_TASKNAME);
		$output->Linebreak();
		$output->BoldText(pnVarPrepForDisplay($task['name']));

        $output->ConfirmAction(_TASKS_CONFIRMDELETE,
                               pnModURL('tasks',
                                        'admin',
                                        'delete'),
                               _TASKS_CANCELDELETE,
                               pnModURL('tasks',
                                        'user',
                                        'view'),
                               array('id' => $id));

        return $output->GetOutput();
    }

    if (pnModAPIFunc('tasks',
                     'admin',
                     'delete',
                     array('id' => $id))) {
        // Success
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_TASKDELETED);
    }

    pnRedirect(pnModURL('tasks', 'user', 'view'));
    
    return true;
}
*/
function tasks_admin_modifyconfig()
{
    $data=array();
 	
	if (!xarModLoad('tasks','user')) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        return true;
    }
	
//     if (!pnSecAuthAction(0, 'tasks::', '', ACCESS_ADMIN)) {
//         pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
//         return true;
//     }
	
    //$output->Text(tasks_menu());

    // Construct maximum depth combobox
    $maxdepthdropdown = array();
	for($x=0; $x<10; $x++) {
		$maxdepthdropdown[] = array('id'=>$x, 'name'=>$x);
	}
    $data['maxdepthdropdown']=$maxdepthdropdown;

    // Construct date format combobox
	$dateformatlist = tasks_dateformatlist();
	$dateformatdropdown = array();
	foreach($dateformatlist as $formatid=>$format) {
		$dateformatdropdown[] = array('id'	=> $formatid,
									'name'	=> strftime($format,time()));
	}
    $data['dateformatdropdown']=$dateformatdropdown;
    $data['dateformat']=xarModGetVar('tasks','dateformat');
    $data['showoptions']= xarModGetVar('tasks','showoptions');

    // WHICH ID TO RETURN DISPLAY TO (CURRENT | PARENT)
	$returnfromoptions = array(array('id' => 0, 'name' => xarML('Current task')),
                               array('id' => 1, 'name' => xarML('Parent task'))
                               );
    $data['returnfromoptions']=$returnfromoptions;
    $data['returnfromadd']=xarModGetVar('tasks','returnfromadd');
    $data['returnfromedit']=xarModGetVar('tasks','returnfromedit');
    $data['returnfromsurface']=xarModGetVar('tasks','returnfromsurface');
    $data['returnfrommigrate']=xarModGetVar('tasks','returnfrommigrate');
    $data['submitbutton']=xarML("Update tasks config");
    return $data;
}

function tasks_admin_updateconfig()
{
    list($dateformat,
		$showoptions,
		$returnfromadd,
		$returnfromedit,
		$returnfromsurface,
		$returnfrommigrate,
		$maxdisplaydepth) = pnVarCleanFromInput('dateformat',
												'showoptions',
												'returnfromadd',
												'returnfromedit',
												'returnfromsurface',
												'returnfrommigrate',
												'maxdisplaydepth');

    pnModSetVar('tasks', 'dateformat', $dateformat);
    pnModSetVar('tasks', 'showoptions', $showoptions);
    pnModSetVar('tasks', 'returnfromadd', $returnfromadd);
    pnModSetVar('tasks', 'returnfromedit', $returnfromedit);
    pnModSetVar('tasks', 'returnfromsurface', $returnfromsurface);
    pnModSetVar('tasks', 'returnfrommigrate', $returnfrommigrate);
    pnModSetVar('tasks', 'maxdisplaydepth', $maxdisplaydepth);

    pnRedirect(pnModURL('tasks', 'admin', 'modifyconfig'));

    return true;
}

function tasks_admin_migrate($args)
{
    list($taskcheck,
		$submit,
		$taskfocus,
		$id,
		$taskoption,
		$modname,
		$objectid,
		$parentid) =	pnVarCleanFromInput('taskcheck',
											'submit',
											'taskfocus',
											'id',
											'taskoption',
											'modname',
											'objectid',
											'parentid');

    extract($args);

    if (!pnModAPILoad('tasks','admin')
			|| !pnModLoad('tasks','user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_LOADFAILED);
        pnRedirect(pnModURL('tasks'));
        return true;
    }

    if($newid = pnModAPIFunc('tasks',
								'admin',
								'migrate',
								array('id'		=> $id,
									'modname'		=> $modname,
									'objectid'		=> $objectid,
									'parentid'		=> $parentid,
									'taskoption'	=> $taskoption,
									'taskcheck'		=> $taskcheck,
									'submit' 		=> $submit,
									'taskfocus'		=> $taskfocus))) {

		pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_MIGRATIONSUCCESSFUL);
	}

	if(empty($newid) || $newid == 0) {
		pnRedirect(pnModURL('tasks',
							'user',
							'view'));
	} else {
		pnRedirect(pnModURL('tasks',
							'user',
							'display',
							array('id' => $newid,
									'modname' => $modname,
									'objectid' => $objectid,
									'' => '#tasklist')));
	}
	
    return true;
}

function tasks_admin_gantt($args)
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
	
	if (!pnModAPILoad('tasks', 'user')
			|| !pnModLoad('tasks', 'admin')) {
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

	include ("gantt/jpgraph.php");
	include ("gantt/jpgraph_gantt.php");
	
	// Some global configs
	$heightfactor=0.5;
	$groupbarheight=0.1;
	$revision="2002-10-14";
	
	// Standard calls to create a new graph
	$graph = new GanttGraph(0,0,"auto");
	$graph->SetShadow();
	$graph->SetBox();
	
	// Titles for chart
	$graph->title->Set("Xaraya scenario roadmap");
	$graph->subtitle->Set("(Revision: $revision)");
	$graph->title->SetFont(FF_FONT1,FS_BOLD,12);
	
	// For illustration we enable all headers. 
	$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
	
	// For the week we choose to show the start date of the week
	// the default is to show week number (according to ISO 8601)
	$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
	
	// Change the scale font 
	$graph->scale->week->SetFont(FF_FONT0);
	$graph->scale->year->SetFont(FF_FONT1,FS_BOLD,12);
	
	// xaroad contains a list of records
	// *id;label;start;duration;predecessor;progress;type;lead;part_of;
	// start: date of start, if empty: today or if predecessor, from end of that one
	// type: 0: grouping; 1: normal task; 2: milestone
	// duration in days
	
	// Algorithm for sophistication
	// - DONE: scan database for group records and keep log of the latest date, so end-date can be set properly
	// - DONE: keep track of predecessors and adjust the start-date of successors
	// - draw arrows from end of predecessor to begin of successors
	
	// Generate the gantt bars
	$plots=array();
	$scenario=array();
	$latestdate = array();
	
	if(is_array($tasks) && count($tasks) > 0) {
		foreach($tasks as $task) {
			switch ($task[type]) {
				case 0: // Grouping record 
					// params: line, label, start, end, caption, heightfactor 
					$bar = new GanttBar($db->recordNr,$task[label],$task[start],"",$task[lead],$groupbarheight);
					$bar->title->SetFont(FF_FONT1,FS_BOLD,8);
					$bar->SetColor("black");
					$bar->SetPattern(BAND_SOLID, "black");
					$scenario[$task[id]]=$bar;
					$plots[$task[id]]=$bar;
					break;
				case 1: // Normal task, indent
					// Calculate end date from start date and duration, if start-date is empty, use today
					if ($task[start]=="") $task[start]=date("Y-m-d");
					if ($task[duration]=="") $task[duration]=0;
					$enddate= date("Y-m-d",(strtotime($task[start])+($task[duration]*24*60*60)));
					if ($enddate > $latestdate[$task[part_of]] ) $latestdate[$task[part_of]]=$enddate;
					$bar = new GanttBar($db->recordNr," ".$task[label],$task[start],$enddate,"[".$task[progress]."%] ".$task[lead],$heightfactor);
					$bar->progress->Set($task[progress]/100);
					$plots[$record[id]]=$bar;
					break;
				case 2: // Milestone
					// pos, label, date, caption
					$ms = new MileStone($db->recordNr,$task[label],$task[start],$task[lead]);
					if ($task[start] > $latestdate[$task[part_of]]) $latestdate[$task[part_of]]=$task[start];
					$ms->title->Setfont(FF_FONT1,FS_BOLD,8);
					$plots[$task[id]]=$ms;
					break;
			}
		}
	
	// Now we have all plots in an array in memory and we can do some processing based on
	// dependencies
	// $plots contains all plot objects
	// 1. Adjust begin dates for objects when they have a predecessor
	// 2. Add lines from predecessor to successor and add them to the plot array
	// 3. Adjust end date of grouping records so line will extend to whole project
		foreach($tasks as $task) {
			if ($task[predecessor]) {
				// Predecessor found, get enddate for that record and set 
				// begindate of current record at least to that date
				$searchrec=array('id' => $task[predecessor]);
				$pred = $db->search($searchrec);
				$earliest = $plots[$pred[id]]->GetMaxDate();
				// Set the begindate of this record to that date
				$plots[$task[id]]->iStart=$earliest;
				$plots[$task[id]]->iEnd=($earliest + $task[duration]*24*60*60);
				// Adjust scenario dates if necessary
				if (date("Y-m-d",$plots[$task[id]]->iEnd) > $latestdate[$plots[$task[partof]]]) {
					$plots[$task[part_of]]->iEnd = $plots[$task[id]]->iEnd;
				}
			}
		}
	}
	
	
	// Add things for which date doesn't change anymore to the graph here.
	// Add a baseline for today
	$vl = new GanttVLine(date("Y-m-d"),"today","darkred");
	$graph->Add($vl);
	
	// Process the plot array for drawing 
	while (list($key, $object) = each($plots)) {
	  $graph->Add($object);
	}
	
	$graph->Stroke();

	$output->SetInputMode(_PNH_VERBATIMINPUT);
}
?>