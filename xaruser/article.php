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
    $article = xarVarPrepForDisplay($article);
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
        $data['deleteurl'] = xarModURL('newsgroups', 'user', 'cancel', 
                                        array('group'     => $group,
                                              'from'      => $data['headers']['From'],
                                              'messageid' => $data['headers']['Message-ID'],
                                              'article'   => $data['articlenum'],
                                              'phase'     => 'confirmed')); //TODO Confirm page
    } else {
        $data['deleteurl'] = '';
    }

    $data['pager'] = xarTplGetPager($data['articlenum'],
                                    $data['counts']['count'],
                                    xarModURL('newsgroups', 'user', 'article', 
                                              array('group' => $group,
                                                    'article' => '%%')
                                              ),
                                    1, // one article (=item) per "page" here
                                    array('firstitem' => $data['counts']['first'],
                                          'blocksize' => 1));
                                          
    // getting the start number for this articles group page
    // youngest article is on top, so adding $numitems-1 to article number 
    $numitems = xarModGetVar('newsgroups', 'numitems');                                     
    $pagergroup =   xarTplPagerInfo($data['articlenum'] + $numitems - 1 ,
                                    $data['counts']['count'],
                                    $numitems, // articles per group page
                                    array('firstitem' => $data['counts']['first'],
                                          'blocksize' => 1));
    $data['groupstartnum'] = $pagergroup['blockfirstitem'];

    // Return the template variables defined in this function
    return $data;
}

?>
