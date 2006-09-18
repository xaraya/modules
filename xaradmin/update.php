<?php

function xproject_admin_update($args)
{
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('reference', 'str::', $reference, $reference, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_name', 'str:1:', $project_name, $project_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str:1:', $private, $private, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str::', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid', 'id', $clientid, $clientid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid', 'id', $memberid, $memberid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectrole', 'str::', $projectrole, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('priority', 'int:1:', $priority, $priority, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importance', 'str::', $importance, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projecttype', 'str::', $projecttype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_approved', 'str::', $date_approved, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planned_start_date', 'str::', $planned_start_date, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planned_end_date', 'str::', $planned_end_date, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('actual_start_date', 'str::', $actual_start_date, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('actual_end_date', 'str::', $actual_end_date, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_planned', 'str::', $hours_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_spent', 'str::', $hours_spent, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_remaining', 'str::', $hours_remaining, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('estimate', 'str::', $estimate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('budget', 'str::', $budget, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('associated_sites', 'array::', $associated_sites, $associated_sites, XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if(!xarModLoad('addressbook', 'user')) return;

    if(empty($returnurl)) $returnurl = xarModURL('xproject', 'admin', 'view');

    if (!xarSecConfirmAuthKey()) return;

    if(!xarModAPIFunc('xproject',
                    'admin',
                    'update',
                    array('projectid'        => $projectid,
                        'project_name'         => $project_name,
                        'reference'         => $reference,
                        'private'            => $private,
                        'description'        => $description,
                        'clientid'            => $clientid,
                        'ownerid'            => $ownerid,
                        'status'            => $status,
                        'priority'            => $priority,
                        'importance'        => $importance,
                        'projecttype'       => $projecttype,
                        'date_approved'        => $date_approved,
                        'planned_start_date'=> $planned_start_date,
                        'planned_end_date'    => $planned_end_date,
                        'actual_start_date' => $actual_start_date,
                        'actual_end_date'    => $actual_end_date,
                        'hours_planned'     => $hours_planned,
                        'hours_spent'        => $hours_spent,
                        'hours_remaining'    => $hours_remaining,
                        'estimate'            => $estimate,
                        'budget'            => $budget,
                        'associated_sites'    => $associated_sites))) {
        return;
    }

    xarSessionSetVar('statusmsg', xarML('Project Updated'));

    if(is_numeric($memberid) && $memberid > 0) {
        if(!xarModAPIFunc('xproject',
                        'team',
                        'create',
                        array('projectid' => $projectid,
                            'memberid' => $memberid,
                            'projectrole' => $projectrole))) {
            // team member added
            xarSessionSetVar('statusmsg', xarML('Team Member added to Project'));
        }
    }

    xarResponseRedirect($returnurl);

    return true;
}

?>