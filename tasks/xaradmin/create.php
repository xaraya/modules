<?php
/**
 * Create a task
 *
 */
function tasks_admin_create($args)
{
	list($parentid,
		$modname,
		$objectid,
		$name,
		$description,
		$status,
		$priority) =	xarVarCleanFromInput('parentid',
											'modname',
											'objectid',
											'name',
											'description',
											'status',
											'priority');

    extract($args);

	// SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
	// PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    $returnid = xarModAPIFunc('tasks','admin','create',
						array('parentid' 	=> $parentid,
                              'modname' 		=> $modname,
                              'objectid' 		=> $objectid,
                              'name' 			=> $name,
                              'status' 		=> $status,
                              'priority' 		=> $priority,
                              'description'	=> $description,
                              'private' => 0));

    if ($returnid != false) {
        // Success
        //xarSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_TASKCREATED);
    }

	xarResponseRedirect(xarModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#addtask')));

    return true;
}

?>