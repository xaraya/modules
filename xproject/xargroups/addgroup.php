<?php

function xproject_groups_addgroup()
{
    $output = new xarHTML();

    $gname = xarVarCleanFromInput('gname');

    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
	
    $gname = xarModAPIFunc('xproject',
			  'groups',
			  'addgroup', array('gname' => $gname));

    if ($gname == false) {
		xarSessionSetVar('errormsg', _GROUPALREADYEXISTS);
		return $output->GetOutput();
    }
	
    xarSessionSetVar('statusmsg', _GROUPADDED);

    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
}

/*
 * deletegroup - delete a group
 * prompts for confirmation
 */
?>