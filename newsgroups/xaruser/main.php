<?php

function newsgroups_user_main()
{
    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    include_once 'modules/newsgroups/xarclass/NNTP.php';

    $data['items'] = array();

    $server     = xarModGetVar('newsgroups', 'server');
    $port       = xarModGetVar('newsgroups', 'port');

    xarTplSetPageTitle(xarVarPrepForDisplay($server));

    $newsgroups = new Net_NNTP();
    $newsgroups -> connect($server, $port);
    $data['items'] = $newsgroups -> getGroups();
    $newsgroups -> quit();

    // Debug
    //var_dump($data['items']);

    $data['server'] = "news://$server";
    // Return the template variables defined in this function
    return $data;
}

?>