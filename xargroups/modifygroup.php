<?php

function xproject_groups_modifygroup()
{
    list($gname,
	 $gid) = xarVarCleanFromInput('gname',
				     'gid');
    $output = new xarHTML();

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "modifygroup") $output->Text(xarModAPIFunc('xproject','user','menu'));

    if (!xarSecAuthAction(0, 'Groups::', '$gname::$gid', ACCESS_EDIT)) {
        $output->Text(_GROUPSADDNOAUTH);
        return $output->GetOutput();
    }

    $output->TableStart(_MODIFYGROUP);
    $output->LineBreak();
    $output->FormStart(xarModURL('xproject', 'groups', 'renamegroup'));
    $output->Text(xarML('Team name'));
    $output->FormText('gname', $gname, 20, 20);
    $output->FormHidden('gid', $gid);
    $output->FormHidden('authid', xarSecGenAuthKey());
    $output->FormSubmit(xarML('Rename group'));
    $output->TableEnd();

    return $output->GetOutput();
}

/*
 * renameGroup - rename group
 * @param $gid - passed to adminapi
 * @param $gname - passed to adminapi
 */
?>