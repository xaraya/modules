<?php

function newsgroups_user_article()
{
    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    xarVarFetch('articleid', 'str:1', $articleid); 
    xarVarFetch('group', 'str:1', $group); 

    include_once 'modules/newsgroups/xarclass/NNTP.php';

    $data['items'] = array();

    $server     = xarModGetVar('newsgroups', 'server');
    $port       = xarModGetVar('newsgroups', 'port');

    $newsgroups = new Net_NNTP();
    $newsgroups -> connect($server, $port);
    $data               = $newsgroups->splitHeaders($articleid);
    $data['article']    = $newsgroups->getBody($articleid);
    $newsgroups -> quit();
    
    $data['article']        = nl2br(xarVarPrepForDisplay($data['article']));
    $data['group']          = $group;
    $data['articleid']      = $articleid;

    // Debug
    //var_dump($data['headers']);

    // Return the template variables defined in this function
    return $data;
}

?>