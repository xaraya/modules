<?php
/**
 * Update task
 *
 */
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
		$hours_remaining) = xarVarCleanFromInput('id',
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

    if($returnid = xarModAPIFunc('tasks',
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
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("Tasks updated"));
    }

    xarRedirect(xarModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#tasklist')));

    return true;
}

?>