<?php

function newsgroups_user_group()
{
    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    $data = array();
    xarVarFetch('group', 'str:1', $data['group']);
    xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED);

    // Fix the input
    $data['group'] = xarVarPrepForDisplay($data['group']);
    xarTplSetPageTitle(xarVarPrepForDisplay($data['group']));

    include_once 'modules/newsgroups/xarclass/NNTP.php';

    $data['items'] = array();
    $data['formats'] = array();

    xarModSetVar('newsgroups', 'numitems', 50);

    $server     = xarModGetVar('newsgroups', 'server');
    $port       = xarModGetVar('newsgroups', 'port');
    $numitems   = xarModGetVar('newsgroups', 'numitems');

    $newsgroups = new Net_NNTP();
    $newsgroups -> connect($server, $port);
    $counts = $newsgroups -> selectGroup($data['group']);
    if (PEAR::isError($counts)) {
        $data['error_message'] = $counts->message;
        $newsgroups->quit();
    } else {
        if (empty($startnum)){
            $startnum = $counts['last'];
        }
        $messages = $newsgroups->getOverview($startnum - $numitems, $startnum);
        $newsgroups->quit();
        $data['items'] = xarModAPIFunc('newsgroups','user','create_threads', $messages);
    }

    //echo '<br /><pre> articles => '; print_r($data['items']); echo '</pre><br />';
   //var_dump($data['items']);

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnum,
                                    $counts['last'],
                                    xarModURL('newsgroups', 'user', 'group', array('group' => $data['group'],'startnum' => '%%')),
                                    $numitems);
// TODO: add support for first number $counts['first'] in pager

    // Debug
    //var_dump($data['items']);

    // Return the template variables defined in this function
    return $data;
}

?>
