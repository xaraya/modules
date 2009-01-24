<?php

function newsgroups_user_article()
{

    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    xarVarFetch('group', 'str:1', $group, NULL, XARVAR_DONT_REUSE, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('article', 'int', $article, 0, XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('messageid', 'str:1', $messageid, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);

    xarTplSetPageTitle($group . ' - ' . $article);

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
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        $msg = xarErrorRender('text', true) . "<br />";
        $msg .= xarML('Article could not be retrieved, displaying newsgroup instead.');
        xarSessionSetVar('statusmsg', $msg);
        xarErrorHandled();
        xarResponseRedirect(xarModURL('newsgroups', 'user', 'group', array('group' => $group)));
        return true;
    }
    if (!isset($data)) return;

    // re-shuffle variables for the template
    $data['articlenum']     = $data['article'];
    $data['article']        = nl2br(xarVarPrepForDisplay($data['body']));

    if(xarSecurityCheck('DeleteNewsGroups', false)) {
        $data['deleteurl'] = xarModURL('newsgroups', 'admin', 'delete',
                                        array('group'     => $group,
                                              'from'      => $data['headers']['From'],
                                              'messageid' => $data['headers']['Message-ID'],
                                              'article'   => $data['articlenum'],
                                              'phase'     => 'confirmed')); //TODO Confirm page
    } else {
        $data['deleteurl'] = '';
    }

    // $data['counts']['count'] may be to small because of deleted articles
    $articlespan = $data['counts']['last'] - $data['counts']['first'] + 1;

    $data['pager'] = xarTplGetPager($data['articlenum'],
                                    $articlespan,
                                    xarModURL('newsgroups', 'user', 'article',
                                              array('group' => $group,
                                                    'article' => '%%')
                                              ),
                                    1, // one article (=item) per "page" here
                                    array('firstitem' => $data['counts']['first'],
                                          'blocksize' => 1));

    // We want a link to the group view page where this particular article is
    // listed. So we need the start article number on that group view page.
    $numitems = xarModGetVar('newsgroups', 'numitems');
    if ($data['counts']['count'] > $numitems) {
        // How many pages are we away from the last item
        $i = floor(($data['counts']['last'] - $data['articlenum']) / $numitems);
        $data['groupstartnum'] = $data['counts']['last'] - $i * $numitems;
    } else {
        $data['groupstartnum'] = Null;
    }

    // Return the template variables defined in this function
    return $data;
}

?>
