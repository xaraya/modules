<?php

function newsgroups_user_main()
{
    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    include_once 'modules/newsgroups/xarclass/NNTP.php';

    $data['items'] = array();

    $server     = xarModGetVar('newsgroups', 'server');
    $port       = xarModGetVar('newsgroups', 'port');
    $wildmat    = xarModGetVar('newsgroups', 'wildmat');

    xarTplSetPageTitle(xarVarPrepForDisplay($server));

    $newsgroups = new Net_NNTP();
    $newsgroups -> connect($server, $port);

// TODO: pre-load complete list of newsgroups and let admin select
//       instead of retrieving the list each time here

    if (empty($wildmat) || !strstr($wildmat,',')) {
        $data['items'] = $newsgroups->getGroups(true, $wildmat);
    } else {
        $matches = explode(',',$wildmat);
        $data['items'] = array();
        foreach ($matches as $match) {
            $items = $newsgroups->getGroups(true, $match);
            $data['items'] = array_merge($data['items'], $items);
        }
    }
    $newsgroups->quit();

    // Debug
    //var_dump($data['items']);

    $data['server'] = "news://$server";
    // Return the template variables defined in this function
    return $data;
}

?>
