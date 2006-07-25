<?php

function xtasks_admin_migrate($args)
{
    list($taskcheck,
		$submit,
		$taskfocus,
		$taskid,
		$taskoption,
		$projectid,
		$parentid) =	xarVarCleanFromInput('taskcheck',
											'submit',
											'taskfocus',
											'taskid',
											'taskoption',
											'projectid',
											'parentid');

    extract($args);

    if (!xarSecConfirmAuthKey()) return;

    if($newtaskid = xarModAPIFunc('xtasks',
								'admin',
								'migrate',
								array('taskid'		=> $taskid,
									'projectid'	=> $projectid,
									'parentid'		=> $parentid,
									'taskoption'	=> $taskoption,
									'taskcheck'		=> $taskcheck,
									'submit' 		=> $submit,
									'taskfocus'		=> $taskfocus))) {

		xarSessionSetVar('statusmsg', xarML('Project(s) Migrated'));
	}

    xarResponseRedirect(xarModURL('xtasks',
						'user',
						'display',
						array('projectid' => $projectid,
								'taskid' => $newtaskid)));

    return true;
}

?>