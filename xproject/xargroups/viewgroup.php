<?php

function xproject_groups_viewgroup($args)
{
	extract($args);
	
	if (!isset($gid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }
	
    $output = new xarHTML();

	if(empty($groupid)) $groupid = xarVarCleanFromInput('gid');
	xarSessionSetVar('groupid',$groupid);
	

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "viewgroup") $output->Text(xarModAPIFunc('xproject','user','menu'));
    $output->LineBreak();

	$group = xarModAPIFunc('xproject','groups','get',array('gid' => $gid));
	
    $output->Title(_USERSINGROUP .': '.  xarVarPrepForDisplay($group['gname']));
    $output->URL(xarModURL('xproject',
						  'groups',
						  'adduser', array('gid' => $group['gid'])),
				_ADDUSERTOGROUP);
    $output->LineBreak();
    $output->SetInputMode(_XH_PARSEINPUT);

    $tableHead = array(_USERNAME, _OPTION);

    $output->TableStart('', $tableHead, 1);

    $users = xarModAPIFunc('xproject',
			  'groups',
			  'getmembers', array('gid' => $group['gid']));
			  
    if ($users == false) {
		$output->Text('No users in this group');
		$output->LineBreak();
		$output->TableEnd();
		return $output->GetOutput();
    }
	
    foreach($users as $user) {
	
		$output->SetOutputMode(_XH_RETURNOUTPUT);
		if (xarSecAuthAction(0, 'Groups::', "::", ACCESS_DELETE)) {
			$action = $output->URL(xarModURL('xproject',
											   'groups',
											   'deleteuser', array('gid'    => $group['gid'],
																   'uid'    => $user['uid'],
																   'authid' => xarSecGenAuthKey())), _DELETE);
		}
		$output->SetOutputMode(_XH_KEEPOUTPUT);
	
		$row = array(xarVarPrepForDisplay($user['uname']), $action);
	
		$output->SetInputMode(_XH_VERBATIMINPUT);
		$output->TableAddRow($row);
		$output->SetInputMode(_XH_PARSEINPUT);
    }
    $output->TableEnd();

    return $output->GetOutput();
}

/*
 * newGroup - create a new group
 * Takes no parameters
 */
?>