<?php

function xproject_groups_renamegroup()
{
    list($gid,
	 $gname,
	 $confirmation) = xarVarCleanFromInput('gid',
					      'gname',
					      'confirmation');

    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
    if (empty($confirmation)) {

	$output = new xarHTML();
	$func = xarVarCleanFromInput('func');
	if($func == "renamegroup") $output->Text(xarModAPIFunc('xproject','user','menu'));
	$output->ConfirmAction(_RENAMEGROUPSURE,
						   xarModURL('xproject', 'groups',
									'renamegroup'),
						   _CANCEL,
						   xarModURL('xproject', 'groups',
									'view'),
						   array('gid' => $gid,
				 'gname' => $gname));

	return $output->GetOutput();
    }
    if (xarModAPIFunc('xproject', 'groups',
		     'renamegroup', array('gid'   => $gid,
					  'gname' => $gname))) {

	xarSessionSetVar('statusmsg', _GROUPRENAMED);
    }
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));

    return true;
}
?>