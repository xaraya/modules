<?php

function xproject_groups_adduser()
{
    $output = new xarHTML();

    $gid = xarVarCleanFromInput('gid');

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "adduser") $output->Text(xarModAPIFunc('xproject','user','menu'));
    $output->LineBreak();
	
	$group = xarModAPIFunc('xproject','groups','get',array('gid' => $gid));

    $output->SetInputMode(_XH_VERBATIMINPUT);
	
    $output->Title(xarML('Add new team member') .' :: '. xarVarPrepForDisplay($group['gname']));
    $output->SetInputMode(_XH_PARSEINPUT);

    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_ADD)) {
		xarSessionSetVar('errormsg', _GROUPSADDNOAUTH);
		xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
	
    $users = (xarModAPIFunc('xproject', 'groups', 'getmembers', array('eid' => $gid)));

    if($users == false) {
		$output->Text(_PMLOGMEMBERSFAILED);
		return $output->GetOutput();
    }
	
    $output->TableStart(xarSessionGetVar('tempvar'));
	xarSessionDelVar('tempvar');
    $output->FormStart(xarModURL('xproject', 'groups', 'insertuser'));
    $output->FormHidden('gid', $group['gid']);
    $output->FormHidden('authid', xarSecGenAuthKey());
    $userlist = array();

    foreach($users as $user) {
	$userlist[] = array('id' => $user['uid'],
			    'name' => $user['uname']);
    }
    $row = array();
    $output->SetOutputMode(_XH_RETURNOUTPUT);
    $row[] = $output->FormSelectMultiple('uid', $userlist);
    $row[] = $output->FormSubmit(xarML('Add member'));
    $output->SetOutputMode(_XH_KEEPOUTPUT);

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $output->TableAddRow($row);
    $output->SetInputMode(_XH_PARSEINPUT);

    $output->FormEnd();
    $output->TableEnd();
    return $output->GetOutput();
}
/*
 * insertuser - insert a user into a group
 */
?>