<?php
/**
 * Open a task
 *
 */
function tasks_admin_open($args)
{
	$id = xarVarCleanFromInput('id');

    extract($args);

	// SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
	// PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    if($returnid = xarModAPIFunc('tasks',
								'admin',
								'open',
								array('id'	=> $id))) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("Tasks updated"));
    }

    xarResponseRedirect(xarModURL('tasks', 'user', 'display', array('id' => $returnid,
															'' => '#tasklist')));

    return true;
}

?>