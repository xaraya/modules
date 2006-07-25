<?php

function xtasks_admin_update($args)
{
    list($taskid,
          $parentid,
          $projectid,
          $task_name,
          $status,
          $priority,
          $importance,
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
          $hours_remaining) =	xarVarCleanFromInput('taskid',
                                            'parentid',
                                            'projectid',
                                            'task_name',
                                            'status',
                                            'priority',
                                            'importance',
                                            'description',
                                            'private',
                                            'creator',
                                            'owner',
                                            'assigner',
                                            'groupid',
                                            'date_created',
                                            'date_approved',
                                            'date_changed',
                                            'date_start_planned',
                                            'date_start_actual',
                                            'date_end_planned',
                                            'date_end_actual',
                                            'hours_planned',
                                            'hours_spent',
                                            'hours_remaining');

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('xtasks',
					'admin',
					'update',
					array('taskid'	            => $taskid,
                        'parentid'              => $parentid,
                        'projectid'             => $projectid,
                        'task_name'             => $task_name,
                        'status'                => $status,
                        'priority'              => $priority,
                        'importance'            => $importance,
                        'description'           => $description,
                        'private'               => $private,
                        'creator'               => $creator,
                        'owner'                 => $owner,
                        'assigner'              => $assigner,
                        'groupid'               => $groupid,
                        'date_created'          => $date_created,
                        'date_approved'         => $date_approved,
                        'date_changed'          => $date_changed,
                        'date_start_planned'    => $date_start_planned,
                        'date_start_actual'     => $date_start_actual,
                        'date_end_planned'      => $date_end_planned,
                        'date_end_actual'       => $date_end_actual,
                        'hours_planned'         => $hours_planned,
                        'hours_spent'           => $hours_spent,
                        'hours_remaining'       => $hours_remaining))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarML('Task Updated'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'view'));

    return true;
}

?>