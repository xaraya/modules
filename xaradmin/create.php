<?php

function xproject_admin_create($args)
{
    list($name,
		$displaydates,
		$displayhours,
		$displayfreq,
		$private,
		$sendmails,
		$importantdays,
		$criticaldays,
		$billable,
		$description) =	xarVarCleanFromInput('name',
											'displaydates',
											'displayhours',
											'displayfreq',
											'private',
											'sendmails',
											'importantdays',
											'criticaldays',
											'billable',
											'description');

    extract($args);
    if (!xarSecConfirmAuthKey()) return;

    $projectid = xarModAPIFunc('xproject',
                        'admin',
                        'create',
                        array('name' 		=> $name,
							'displaydates'	=> $displaydates,
							'displayhours'	=> $displayhours,
							'displayfreq'	=> $displayfreq,
							'private'		=> $private,
							'sendmails'		=> $sendmails,
							'importantdays'	=> $importantdays,
							'criticaldays'	=> $criticaldays,
							'billable'		=> $billable,
							'description'	=> $description));


	if (!isset($projectid) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

	xarSessionSetVar('statusmsg', xarMLByKey('PROJECTCREATED'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));
//    xarResponseRedirect(xarModURL('xproject', 'user', 'display', array('projectid' => $projectid)));

    return true;
}

?>