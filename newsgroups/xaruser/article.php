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

    $headers               = $newsgroups->getHeaders($articleid);
    $article               = $newsgroups->splitHeaders($articleid);
    if (PEAR::isError($article)) {
        $data['error_message'] = $article->message;
    } else {

        $data['headers']    = $newsgroups->getHeaders($articleid);
        $data['article']    = $newsgroups->getBody($articleid);
        $newsgroups -> quit();

        $data['article']        = nl2br(xarVarPrepForDisplay($data['article']));
        $data['group']          = $group;
        $data['articleid']      = $articleid;

        $header_list = explode("\n", $headers);
        $headers = array();
        foreach ($header_list as $header) {
           if (preg_match("/^([a-zA-Z0-9_-]*): (.*)/", $header, $matches)) {
              $key = $matches[1];
              $headers[$key] = $matches[2];
           } else {
              if (isset($key)) {
                 $headers[$key] .= $header;
              }
           }
       }
       unset($header_list);

       $data['headers']        = $headers;
    }
    // Debug
   //  var_dump($headers);

    // Return the template variables defined in this function
    return $data;
}

?>
