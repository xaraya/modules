<?php

function xproject_admin_update($args)
{
    list($projectid,
		$name,
		$displaydates,
		$displayhours,
		$displayfreq,
		$private,
		$sendmailfreq,
		$importantdays,
		$criticaldays,
		$billable,
		$description) =	xarVarCleanFromInput('projectid',
											'name',
											'displaydates',
											'displayhours',
											'displayfreq',
											'private',
											'sendmailfreq',
											'importantdays',
											'criticaldays',
											'billable',
											'description');

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('xproject',
					'admin',
					'update',
					array('projectid'	=> $projectid,
						'name' 			=> $name,
						'displaydates'	=> $displaydates,
						'displayhours'	=> $displayhours,
						'displayfreq'	=> $displayfreq,
						'private'		=> $private,
						'sendmailfreq'	=> $sendmailfreq,
						'importantdays'	=> $importantdays,
						'criticaldays'	=> $criticaldays,
						'billable'		=> $billable,
						'description'	=> $description))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarMLByKey('Project Updated'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));
//    xarResponseRedirect(xarModURL('xproject', 'user', 'display', array('projectid' => $projectid)));

    return true;
}

?>