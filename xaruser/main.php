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

    $data['server'] = "news://$server";
    xarTplSetPageTitle(xarVarPrepForDisplay($server));

    $newsgroups = new Net_NNTP();
    $rs = $newsgroups -> connect($server, $port);
    if (PEAR::isError($rs)) {
        $data['error_message'] = $rs->message;
        $newsgroups->quit();
        return $data;
    }

// TODO: pre-load complete list of newsgroups and let admin select
//       instead of retrieving the list each time here

    if (empty($wildmat) || !strstr($wildmat,',')) {
        $data['items'] = $newsgroups->getGroups(true, $wildmat);
        if (PEAR::isError($data['items'])) {
            $data['error_message'] = $data['items']->message;
            $data['items'] = array();
        }
    } else {
        $matches = explode(',',$wildmat);
        $data['items'] = array();
        foreach ($matches as $match) {
            $items = $newsgroups->getGroups(true, $match);
            if (PEAR::isError($items)) {
                $data['error_message'] = $items->message;
                break;
            }
            $data['items'] = array_merge($data['items'], $items);
        }
    }
    $newsgroups->quit();

    // Debug
    //var_dump($data['items']);

    // Return the template variables defined in this function
    return $data;
}

?>
