<?php

function newsgroups_user_article()
{

    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    xarVarFetch('group', 'str:1', $group);
    xarVarFetch('article', 'int', $article, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('messageid', 'str:1', $messageid, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);

    // fix the input
    $group = xarVarPrepForDisplay($group);
    xarTplSetPageTitle($group);

    if (empty($article) && empty($messageid)) {
        xarResponseRedirect(xarModURL('newsgroups', 'user', 'group',
                                      array('group' => $group)));
        return true;
    }

    $data = xarModAPIFunc('newsgroups','user','getarticle',
                          array('group'     => $group,
                                'article'   => $article,
                                'messageid' => $messageid,
                                'getrefnum' => true));
    if (!isset($data)) return;

    // re-shuffle variables for the template
    $data['articlenum']     = $data['article'];
    $data['article']        = nl2br(xarVarPrepForDisplay($data['body']));

    $data['pager'] = xarTplGetPager($data['articlenum'],
                                    $data['counts']['last'],
                                    xarModURL('newsgroups', 'user', 'article', array('group' => $group,'article' => '%%')),
                                    1, // one article per "page" here
                                    1); // one "page" per block here
// TODO: add support for first number $counts['first'] in pager

    // Return the template variables defined in this function
    return $data;
}

?>
