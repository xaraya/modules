<?php

function newsgroups_user_article()
{

    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    xarVarFetch('group', 'str:1', $group);
    xarVarFetch('article', 'str:1', $article, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('messageid', 'str:1', $messageid, '', XARVAR_NOT_REQUIRED);

    if (empty($article) && empty($messageid)) {
        xarResponseRedirect(xarModURL('newsgroups', 'user', 'group',
                                      array('group' => $group)));
        return true;
    }

    include_once 'modules/newsgroups/xarclass/NNTP.php';

    $server     = xarModGetVar('newsgroups', 'server');
    $port       = xarModGetVar('newsgroups', 'port');

    $newsgroups = new Net_NNTP();
    $newsgroups->connect($server, $port);

    $data = array();
    $data['group'] = $group;
    $data['pager'] = '';

    $counts = $newsgroups->selectGroup($group);
    if (PEAR::isError($counts)) {
        $data['error_message'] = $counts->message;
        $newsgroups->quit();
        return $data;
    }

    xarTplSetPageTitle(xarVarPrepForDisplay($data['group']));

    if (empty($article) && !empty($messageid)) {
        $headers = $newsgroups->splitHeaders($messageid);
        if (!empty($headers['Xref']) && preg_match("/ $group:(\d+)/",$headers['Xref'],$matches)) {
            $article = $matches[1];
        } else {
            $article = $messageid;
        }
    } else {
        $headers = $newsgroups->splitHeaders($article);
    }
    if (PEAR::isError($headers)) {
        $data['error_message'] = $headers->message;
        $newsgroups->quit();
        return $data;
    }

// TODO: translate References to article numbers
    $data['headers']    = $headers;
    $data['article']    = $newsgroups->getBody($article);
    $newsgroups->quit();

    $data['article']        = nl2br(xarVarPrepForDisplay($data['article']));
    $data['articlenum']     = $article;

    $data['pager'] = xarTplGetPager($article,
                                    $counts['last'],
                                    xarModURL('newsgroups', 'user', 'article', array('group' => $data['group'],'article' => '%%')),
                                    1, // one article per "page" here
                                    1); // one "page" per block here
// TODO: add support for first number $counts['first'] in pager

    // Debug
    // echo '<br /><pre>';print_r($headers);echo '</pre><br />';

    // Return the template variables defined in this function
    return $data;
}

?>
