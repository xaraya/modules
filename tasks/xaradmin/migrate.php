<?php
/**
 * Migrate a task
 *
 */
function tasks_admin_migrate($args)
{
    list($taskcheck,
		$submit,
		$taskfocus,
		$id,
		$taskoption,
		$modname,
		$objectid,
		$parentid) =	xarVarCleanFromInput('taskcheck',
											'submit',
											'taskfocus',
											'id',
											'taskoption',
											'modname',
											'objectid',
											'parentid');

    extract($args);

    if($newid = xarModAPIFunc('tasks',
								'admin',
								'migrate',
								array('id'		=> $id,
									'modname'		=> $modname,
									'objectid'		=> $objectid,
									'parentid'		=> $parentid,
									'taskoption'	=> $taskoption,
									'taskcheck'		=> $taskcheck,
									'submit' 		=> $submit,
									'taskfocus'		=> $taskfocus))) {

		xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("Task migration successfull"));
	}

	if(empty($newid) || $newid == 0) {
		xarResponseRedirect(xarModURL('tasks','user','view'));
	} else {
		xarResponseRedirect(xarModURL('tasks','user','display',
							array('id' => $newid,
									'modname' => $modname,
									'objectid' => $objectid,
									'' => '#tasklist')));
	}
	
    return true;
}

?>