<?php

function newsgroups_user_article()
{

    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    xarVarFetch('articleid', 'str:1', $articleid);
    xarVarFetch('group', 'str:1', $group);

    include_once 'modules/newsgroups/xarclass/NNTP.php';

    $server     = xarModGetVar('newsgroups', 'server');
    $port       = xarModGetVar('newsgroups', 'port');

    $newsgroups = new Net_NNTP();
    $newsgroups->connect($server, $port);

    $headers = $newsgroups->splitHeaders($articleid);

    if (PEAR::isError($headers)) {
        $data['error_message'] = $headers->message;
    } else {

        $data['headers']    = $headers;
        $data['article']    = $newsgroups->getBody($articleid);
        $newsgroups -> quit();

        $data['article']        = nl2br(xarVarPrepForDisplay($data['article']));
        $data['group']          = $group;
        $data['articleid']      = $articleid;
    }


    // Debug
    // echo '<br /><pre>';print_r($headers);echo '</pre><br />';

    // Return the template variables defined in this function
    return $data;
}

?>
