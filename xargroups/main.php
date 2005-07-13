<?php

/**
 * the main administration function - pass-thru
 */
function xproject_groups_main()
{
    $output = new xarHTML();

    // auth check
    if (!xarSecAuthAction(0, 'Groups::', '::', ACCESS_DELETE)) {
        $output->Text(_GROUPSNOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if(($func == "main" || empty($func))) $output->Text(xarModAPIFunc('xproject','user','menu'));

    return $output->GetOutput();
}

?>