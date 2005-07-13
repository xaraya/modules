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

?>