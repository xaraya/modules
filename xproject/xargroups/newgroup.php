<?php

function xproject_groups_newgroup()
{
    $output = new xarHTML();

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "newgroup") $output->Text(xarModAPIFunc('xproject','user','menu'));

    if (!xarSecAuthAction(0, 'Groups::', '::', ACCESS_ADD)) {
        $output->Text(_GROUPSADDNOAUTH);
        return $output->GetOutput();
    }
    $output->FormStart(xarModURL('xproject', 'groups', 'addgroup'));
    $output->LineBreak();
    $output->Text(_GROUXARAME);
    $output->FormText('gname', '', 20, 20);
    $output->FormHidden('authid', xarSecGenAuthKey());
    $output->LineBreak(2);
    $output->FormSubmit(_NEWGROUP);
    $output->FormEnd();

    return $output->GetOutput();
}

/*
 * addGroup - add a group
 */
?>