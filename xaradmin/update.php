<?php

function xtasks_admin_update($args)
{
    extract($args);
    
    if (!xarVarFetch('taskid', 'id', $taskid)) return;
    if (!xarVarFetch('task_name', 'str:1:', $task_name, $task_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str:1:', $private, $private, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str::', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owner', 'id', $owner, $owner, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('assigner', 'id', $assigner, $assigner, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('groupid', 'id', $groupid, $groupid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('priority', 'int:1:', $priority, $priority, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importance', 'str::', $importance, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_created', 'str::', $date_created, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_approved', 'str::', $date_approved, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_changed', 'str::', $date_changed, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_start_planned', 'str::', $date_start_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_start_actual', 'str::', $date_start_actual, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_end_planned', 'str::', $date_end_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_end_actual', 'str::', $date_end_actual, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_planned', 'float::', $hours_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_spent', 'float::', $hours_spent, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_remaining', 'float::', $hours_remaining, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if(empty($returnurl)) $returnurl = xarModURL('xtasks', 'admin', 'view');
    
    if (!xarSecConfirmAuthKey()) return;
    
    if(!empty($cancel)) {
        xarResponseRedirect($returnurl);
    }
    
    if(!xarModAPIFunc('xtasks',
                    'admin',
                    'update',
                    array('taskid'                => $taskid,
                        'task_name'             => $task_name,
                        'status'                => $status,
                        'priority'              => $priority,
                        'importance'            => $importance,
                        'description'           => $description,
                        'private'               => $private,
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

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }
    
    xarResponseRedirect(xarModURL('xtasks', 'admin', 'view'));

    return true;
}

?>