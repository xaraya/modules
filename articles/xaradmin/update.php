<?php

/**
 * update item from articles_admin_modify
 */
function articles_admin_update()
{
    // Get parameters
    if(!xarVarFetch('aid',      'isset', $aid,       NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('ptid',     'isset', $ptid,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cids',     'isset', $cids,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('preview',  'isset', $preview,   NULL, XARVAR_DONT_SET)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if (empty($aid) || !is_numeric($aid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item id', 'admin', 'update', 'Articles');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
    if (empty($ptid) || !isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'publication type', 'admin', 'update', 'Articles');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Get original article information
    $article = xarModAPIFunc('articles',
                            'user',
                            'get',
                            array('aid' => $aid));

    if (!isset($article)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'article', 'admin', 'update', 'Articles');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

// TODO: switch to DD object style
    $invalid = array();
    if (xarModIsHooked('uploads', 'articles', $ptid)) {
        xarVarSetCached('Hooks.uploads','ishooked',1);
    }
    $properties = array();
    foreach ($pubtypes[$ptid]['config'] as $field => $value) {
        if (!empty($value['label'])) {
            if (!isset($value['validation'])) {
                $value['validation'] = '';
            }
            $properties[$field] =& xarModAPIFunc('dynamicdata','user','getproperty',
                                                 array('name' => $field,
                                                       'type' => $value['format'],
                                                       'validation' => $value['validation'],
                                                       'value' => $article[$field]));
            $check = $properties[$field]->checkInput($field);
            if (!$check) {
                $article[$field] = '';
                $invalid[$field] = $properties[$field]->invalid;
                $preview = 1;
            } else {
                $article[$field] = $properties[$field]->value;
            }
        }
        if (!isset($article[$field])) {
            $article[$field] = '';
        }
    }

    $article['ptid'] = $ptid;

    // check that we have a title when we need one, or fill in a dummy one
    if (empty($article['title'])) {
        if (empty($pubtypes[$ptid]['config']['title']['label'])) {
            $article['title'] = ' ';
        } elseif (empty($invalid['title'])) {
            // show this to the user
            $invalid['title'] = xarML('This field is required');
        }
    }
    if (empty($article['pubdate'])) {
        $article['pubdate'] = 0;
    }

// TODO: make $status dependent on permissions ?
    if (empty($article['status'])) {
        if (empty($pubtypes[$ptid]['config']['status']['label'])) {
            $article['status'] = 2;
        } else {
            $article['status'] = 0;
        }
    }

    if (empty($article['language'])) {
        $article['language'] = 'eng';
    }

    if (!empty($cids) && count($cids) > 0) {
        $article['cids'] = array_values(preg_grep('/\d+/',$cids));
    } else {
        $article['cids'] = array();
    }

    // for preview
    $article['pubtypeid'] = $ptid;
    $article['aid'] = $aid;

    if ($preview || count($invalid) > 0) {
        $data = xarModFunc('articles','admin','modify',
                             array('preview' => true,
                                   'article' => $article,
                                   'invalid' => $invalid));
        unset($article);
        if (is_array($data)) {
            return xarTplModule('articles','admin','modify',$data);
        } else {
            return $data;
        }
    }

    // call transform input hooks
    $article['transform'] = array('summary','body','notes');
    $article = xarModCallHooks('item', 'transform-input', $aid, $article,
                               'articles', $ptid);

    // Pass to API
    if (!xarModAPIFunc('articles', 'admin', 'update', $article)) {
        return;
    }
    unset($article);

    // Success
    xarSessionSetVar('statusmsg', xarML('Article Updated'));

    // Return to the original admin view
    $lastview = xarSessionGetVar('Articles.LastView');
    if (isset($lastview)) {
        $lastviewarray = unserialize($lastview);
        if (!empty($lastviewarray['ptid']) && $lastviewarray['ptid'] == $ptid) {
            extract($lastviewarray);
            xarResponseRedirect(xarModURL('articles', 'admin', 'view',
                                          array('ptid' => $ptid,
                                                'catid' => $catid,
                                                'status' => $status,
                                                'startnum' => $startnum)));
            return true;
        }
    }

    // if we can edit articles, go to admin view, otherwise go to user view
    if (xarSecurityCheck('EditArticles',0,'Article',$ptid.':All:All:All')) {
        xarResponseRedirect(xarModURL('articles', 'admin', 'view',
                                      array('ptid' => $ptid)));
    } else {
        xarResponseRedirect(xarModURL('articles', 'user', 'view',
                                      array('ptid' => $ptid)));
    }

    return true;
}

?>
