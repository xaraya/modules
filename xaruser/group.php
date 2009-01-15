<?php

function newsgroups_user_group()
{
    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    xarVarFetch('group', 'str:1', $group);
    xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('sortby', 'enum:thread:article', $sortby, '', XARVAR_NOT_REQUIRED);
    if (empty($sortby)) {
        $sortby = xarModGetVar('newsgroups','sortby');
    }

    // Fix the input
    $group = xarVarPrepForDisplay($group);
    xarTplSetPageTitle($group);

    $numitems = xarModGetVar('newsgroups', 'numitems');

    $data = xarModAPIFunc('newsgroups','user','getoverview',
                          array('group'    => $group,
                                'startnum' => $startnum,
                                'numitems' => $numitems,
                                'sortby'   => $sortby));
    if (!isset($data)) return;

    if (empty($startnum)){
        $startnum = $data['counts']['last'];
    }

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnum,
                                    $data['counts']['count'],  //['last'],
                                    xarModURL('newsgroups', 'user', 'group', 
                                              array('group' => $group,
                                                    'startnum' => '%%')),
                                    $numitems, // articles per page
                                    array('firstitem' => $data['counts']['first'],
                                          'blocksize' => 5)
                                    );

    // Return the template variables defined in this function
    return $data;
}

?>
