<?php
function xproject_tasks_create($args)
{
    list($projectid,
        $name,
		$parentid,
        $groupid,
		$priority,
        $status,
        $description,
		$private,
        $owner,
        $assigner,
        $date_approved,
        $date_changed,
        $date_start_planned,
        $date_start_actual,
        $date_end_planned,
		$hours_planned,
		$hours_spent,
		$hours_remaining,
		$cost,
		$recurring,
		$periodicity,
		$reminder) = xarVarCleanFromInput('projectid',
									   'name',
									   'parentid',
									   'groupid',
									   'priority',
									   'status',
									   'description',
									   'private',
									   'owner',
									   'assigner',
									   'date_approved',
									   'date_changed',
									   'date_start_planned',
									   'date_start_actual',
									   'date_end_planned',
									   'hours_planned',
									   'hours_spent',
									   'hours_remaining',
									   'cost',
									   'recurring',
									   'periodicity',
									   'reminder');

    extract($args);

    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item'." *$authid*",
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarModAPILoad('xproject', 'tasks')) return;

    $taskid = xarModAPIFunc('xproject',
                        'tasks',
                        'create',
                        array('projectid' => $projectid,
							 'name' => $name,
							 'parentid' => $parentid,
							 'groupid' => $groupid,
							 'priority' => $priority,
							 'status' => $status,
							 'description' => $description,
							 'private' => $private,
							 'owner' => $owner,
							 'assigner' => $assigner,
							 'date_approved' => $date_approved,
							 'date_changed' => $date_changed,
							 'date_start_planned' => $date_start_planned,
							 'date_start_actual' => $date_start_actual,
							 'date_end_planned' => $date_end_planned,
							 'hours_planned' => $hours_planned,
							 'hours_spent' => $hours_spent,
							 'hours_remaining' => $hours_remaining));


	if (!isset($taskid) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

	xarSessionSetVar('statusmsg', xarMLByKey('PROJECTCREATED'));

    xarResponseRedirect(xarModURL('xproject',
						'user',
						'display',
						array('projectid' => $projectid,
								'taskid' => $parentid)));
//    xarResponseRedirect(xarModURL('xproject', 'tasks', 'display', array('tid' => $projectid)));

    return true;
}

function xproject_tasks_modify($args)
{
    list($startnum,
         $taskid,
         $objectid) = xarVarCleanFromInput('startnum',
                                          'taskid',
                                          'objectid');

	extract($args);
	
    if (!empty($objectid)) {
        $taskid = $objectid;
    }
	
	if (!xarModAPILoad('xproject', 'tasks')) return;
	if (!xarModAPILoad('xproject', 'user')) return;
	if (!xarModLoad('xproject', 'user')) return;
	
	$data = xproject_user_menu();
	
	$data['status'] = '';
	
	$task = xarModAPIFunc('xproject',
                         'tasks',
                         'get',
                         array('taskid' => $taskid));
	
	if (!isset($task) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    $project = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $task['projectid']));

    if (isset($project['projectid']) && xarExceptionMajor() == XAR_NO_EXCEPTION) {
		list($project['name']) = xarModCallHooks('item',
											 'transform',
											 $project['projectid'],
											 array($project['name']));
	
		$data['name'] = xarVarCensor($project['name']);
		$data['description'] = xarVarCensor($project['description']);
	}

    if (!xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$taskid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to modify #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
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
		$data['taskparent'] = $parent['name'];
		$data['taskparent_id'] = $parent['taskid'];
	} else {
		$data['taskparent'] = xarMLByKey('Project Top');
		$data['taskparent_id'] = 0;
	}

	$data['projectid'] = $task['projectid'];
    $data['authid'] = xarSecGenAuthKey();
    $data['taskid'] = $taskid;
	$data['parentid'] = $task['parentid'];

	$data['taskname'] = $task['name'];
	$data['description'] = $task['description'];

	$statusoptions = array();    
	$statusoptions[] = array('id'=>0,'name'=>xarMLByKey('Open'),'value'=>0);
	$statusoptions[] = array('id'=>1,'name'=>xarMLByKey('Closed'),'value'=>1);
	$data['statusoptions'] = $statusoptions;
	$data['status'] = $task['status'];

	$data['priority'] = $task['priority'];

	$data['updatebutton'] = xarVarPrepForDisplay(xarMLByKey('Modify'));

    $item = array();
	$item['module'] = 'xproject';
    $hooks = xarModCallHooks('item','modify',$taskid,$item);
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }
    $data['hookoutput'] = $hooks;

	$data['tasks'] = array();

	$tasks = xarModAPIFunc('xproject',
							'tasks',
							'getall',
							array('startnum' => $startnum,
								'projectid' => $task['projectid'],
								'parentid' => $taskid));
	if (isset($tasks) && is_array($tasks) && (xarExceptionMajor() == XAR_NO_EXCEPTION)) {
		for ($i = 0; $i < count($tasks); $i++) {
			$task = $tasks[$i];
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
		$data['numtasks'] = count($tasks);
	}

	return $data;
}

function xproject_tasks_update($args)
{
    list($taskid,
		$projectid,
        $name,
		$parentid,
        $groupid,
		$priority,
        $status,
        $description,
		$private,
        $owner,
        $assigner,
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
		$reminder) = xarVarCleanFromInput('taskid',
										'projectid',
										'name',
										'parentid',
										'groupid',
										'priority',
										'status',
										'description',
										'private',
										'owner',
										'assigner',
										'date_approved',
										'date_changed',
										'date_start_planned',
										'date_start_actual',
										'date_end_planned',
										'date_end_actual',
										'hours_planned',
										'hours_spent',
										'hours_remaining',
										'cost',
										'recurring',
										'periodicity',
										'reminder');

    extract($args);

    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($taskid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarModAPILoad('xproject', 'tasks')) return;

    if(!xarModAPIFunc('xproject',
					'tasks',
					'update',
					array('taskid' => $taskid,
						 'projectid' => $projectid,
						 'name' => $name,
						 'parentid' => $parentid,
						 'groupid' => $groupid,
						 'priority' => $priority,
						 'status' => $status,
						 'description' => $description,
						 'private' => $private,
						 'owner' => $owner,
						 'assigner' => $assigner,
						 'date_approved' => $date_approved,
						 'date_changed' => $date_changed,
						 'date_start_planned' => $date_start_planned,
						 'date_start_actual' => $date_start_actual,
						 'date_end_planned' => $date_end_planned,
						 'hours_planned' => $hours_planned,
						 'hours_spent' => $hours_spent,
						 'hours_remaining' => $hours_remaining))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarMLByKey('PROJECTUPDATED'));

    xarResponseRedirect(xarModURL('xproject', 'user', 'display', array('projectid' => $projectid, 'taskid' => $parentid)));
//    xarResponseRedirect(xarModURL('xproject', 'tasks', 'display', array('tid' => $tid)));

    return true;
}

function xproject_tasks_delete($args)
{
    list($taskid,
         $objectid,
         $confirm) = xarVarCleanFromInput('taskid',
										  'objectid',
										  'confirm');

    extract($args);

     if (!empty($objectid)) {
         $taskid = $objectid;
     }                     

    if (!xarModAPILoad('xproject', 'tasks')) return;
    if (!xarModLoad('xproject', 'user')) return;

    $task = xarModAPIFunc('xproject',
                         'tasks',
                         'get',
                         array('taskid' => $taskid));

    if (!isset($task) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$taskid", ACCESS_DELETE)) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($tid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (empty($confirm)) {
        $data = xproject_user_menu();

        $data['projectid'] = $task['projectid'];
        $data['taskid'] = $task['taskid'];

        $data['taskname'] = xarVarPrepForDisplay($task['name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
	}
	if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	if (!xarModAPILoad('xproject', 'tasks')) return;
    if (!xarModAPIFunc('xproject',
                     'tasks',
                     'delete',
                     array('taskid' => $taskid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarMLByKey('Task Deleted'));

    xarResponseRedirect(xarModURL('xproject', 'user', 'display', array('projectid' => $task['projectid'], 'taskid' => $task['parentid'])));

    return true;
}
?>