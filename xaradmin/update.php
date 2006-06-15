<?php

function xproject_admin_update($args)
{
    list($projectid,
        $project_name,
        $private,
        $description,
        $clientid,
        $ownerid,
        $status,
        $priority,
        $importance,
        $date_approved,
        $planned_start_date,
        $planned_end_date,
        $actual_start_date,
        $actual_end_date,
        $hours_planned,
        $hours_spent,
        $hours_remaining,
        $associated_sites) =	xarVarCleanFromInput('projectid',
                                            'project_name',
                                            'private',
                                            'description',
                                            'clientid',
                                            'ownerid',
                                            'status',
                                            'priority',
                                            'importance',
                                            'date_approved',
                                            'planned_start_date',
                                            'planned_end_date',
                                            'actual_start_date',
                                            'actual_end_date',
                                            'hours_planned',
                                            'hours_spent',
                                            'hours_remaining',
                                            'associated_sites');

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('xproject',
					'admin',
					'update',
					array('projectid'	    => $projectid,
						'project_name' 	    => $project_name,
                        'private'	        => $private,
                        'description'	    => $description,
                        'clientid'	        => $clientid,
                        'ownerid'	        => $ownerid,
                        'status'	        => $status,
                        'priority'		    => $priority,
                        'importance'		=> $importance,
                        'date_approved'	    => $date_approved,
                        'planned_start_date'=> $planned_start_date,
                        'planned_end_date'	=> $planned_end_date,
                        'actual_start_date' => $actual_start_date,
                        'actual_end_date'	=> $actual_end_date,
                        'hours_planned'     => $hours_planned,
                        'hours_spent'		=> $hours_spent,
                        'hours_remaining'	=> $hours_remaining,
                        'associated_sites'	=> $associated_sites))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarML('Project Updated'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));

    return true;
}

?>