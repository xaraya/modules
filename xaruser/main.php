<?php

function newsgroups_user_main()
{
    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    $server = xarModGetVar('newsgroups', 'server');
    $data['server'] = "news://$server";
    xarTplSetPageTitle(xarVarPrepForDisplay($server));

    $data['items'] = xarModAPIFunc('newsgroups','user','getgroups');
    if (!isset($data['items'])) return;

    // Return the template variables defined in this function
    return $data;
}

?>
