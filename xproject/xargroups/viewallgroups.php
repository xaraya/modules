<?php

/**
 * view groups
 */
function xproject_groups_viewallgroups()
{
    $output = new xarHTML();

	xarSessionSetVar('groupid',0);
	xarSessionDelVar('grouxarame');
	
    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "viewallgroups") $output->Text(xarModAPIFunc('xproject','user','menu'));

    $groups = xarModAPIFunc('xproject',
			   'groups',
			   'getall');

    $tableHead = array(_GROUP, _OPTION);

    $output->TableStart('', $tableHead, 1);

    foreach($groups as $group) {

	$actions = array();
	$output->SetOutputMode(_XH_RETURNOUTPUT);

	if (xarSecAuthAction(0, 'Groups::', "$group[name]::$group[gid]", ACCESS_EDIT)) {
	    $grouxaramedisplay = $output->URL(xarModURL('xproject',
											   'groups',
											   'viewgroup', array('gid'   => $group['gid'],
													  'gname' => $group['name'])), xarVarPrepForDisplay($group['name']));
	} else {
		$grouxaramedisplay = $output->Text(xarVarPrepForDisplay($group['name']));
	}
	
	if (xarSecAuthAction(0, 'Groups::', "$group[name]::$group[gid]", ACCESS_EDIT)) {
	    $actions[] = $output->URL(xarModURL('xproject',
					       'groups',
					       'modifygroup', array('gid'   => $group['gid'],
								    'gname' => $group['name'])), _RENAMEGROUP);

	}
	if (xarSecAuthAction(0, 'Groups::', "$group[name]::$group[gid]", ACCESS_DELETE)) {
	    $actions[] = $output->URL(xarModURL('xproject',
					       'groups',
					       'deletegroup', array('gid'    => $group['gid'],
								    'gname'  => $group['name'],
								    'authid' => xarSecGenAuthKey())), _DELETE);
	}
	$output->SetOutputMode(_XH_KEEPOUTPUT);

	$actions = join(' | ', $actions);

	$row = array($grouxaramedisplay,
		     $actions);

	$output->SetInputMode(_XH_VERBATIMINPUT);
	$output->TableAddRow($row);
	$output->SetInputMode(_XH_PARSEINPUT);
    }
    $output->TableEnd();

    return $output->GetOutput();
}

/*
 * viewgroup - view a group
 */
?>