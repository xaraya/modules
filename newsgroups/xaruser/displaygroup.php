<?php

function newsgroups_user_displaygroup()
{
    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    xarVarFetch('group', 'str:1', $data['group']); 
    xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_NOT_REQUIRED);

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
    if ($startnumitem == NULL){
        $startnumitem = $counts['last'];
    }
    $data['items'] = $newsgroups -> getOverview($startnumitem - $numitems, $counts['last']);
    $newsgroups -> quit();

    $data['items'] = array_reverse($data['items']);

    //var_dump($data['items']);

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnumitem,
                                    $counts['last'],
                                    xarModURL('newsgroups', 'user', 'displaygroup', array('startnumitem' => '%%')),
                                    $numitems);

    // Debug
    //var_dump($data['items']);

    // Return the template variables defined in this function
    return $data;
}

?>