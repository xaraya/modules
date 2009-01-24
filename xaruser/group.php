<?php

function newsgroups_user_group()
{
    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    xarVarFetch('group', 'str:1', $group, NULL, XARVAR_DONT_REUSE, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('sortby', 'enum:thread:article', $sortby, '', XARVAR_NOT_REQUIRED);
    if (empty($sortby)) {
        $sortby = xarModGetVar('newsgroups','sortby');
    }

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

    // Call the xarTPL helper function to produce a pager.
    // $data['counts']['count'] may be to small because of deleted articles
    $articlespan = $data['counts']['last'] - $data['counts']['first'] + 1;

    // Newsgroups are listed backwards. To let the last page show as much
    // articles as wanted in $numitems we have to correct the firstitem
    $firstitem = $data['counts']['last'] - floor($articlespan / $numitems) * $numitems;

    $data['pager'] = xarTplGetPager($startnum,
                                    $articlespan,
                                    xarModURL('newsgroups', 'user', 'group',
                                              array('group' => $group,
                                                    'startnum' => '%%')),
                                    $numitems, // articles per page
                                    array('firstitem' => $firstitem,
                                          'blocksize' => 5)
                                    );

    // Return the template variables defined in this function
    return $data;
}

?>
