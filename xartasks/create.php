<?php

function xtasks_tasks_create($args)
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
        return;
    }

    $taskid = xarModAPIFunc('xtasks',
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


    if (!isset($taskid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarML('Task Created'));

    xarResponseRedirect(xarModURL('xtasks',
                        'user',
                        'display',
                        array('projectid' => $projectid,
                                'taskid' => $parentid)));
//    xarResponseRedirect(xarModURL('xtasks', 'tasks', 'display', array('tid' => $projectid)));

    return true;
}

?>
