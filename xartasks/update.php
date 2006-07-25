<?php

function xtasks_tasks_update($args)
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
                    'xtasks', xarVarPrepForDisplay($taskid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if(!xarModAPIFunc('xtasks',
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

    xarResponseRedirect(xarModURL('xtasks', 'user', 'display', array('projectid' => $projectid, 'taskid' => $parentid)));
//    xarResponseRedirect(xarModURL('xtasks', 'tasks', 'display', array('tid' => $tid)));

    return true;
}

?>
