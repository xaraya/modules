<?php

/**
 * search articles (called as hook from search module, or directly with pager)
 *
 * @param $args['objectid'] could be the query ? (currently unused)
 * @param $args['extrainfo'] all other parameters ? (currently unused)
 * @returns output
 */
function articles_user_search($args)
{
// TODO: clean up this parameter list
    // Get parameters
    list($startnum,
     // categories stuff
         $cids,
         $andcids,
         $catid,
     // single publication type when called via the pager
         $ptid,
     // multiple publication types when called via search hooks
         $ptids,
     // yes, this is the query
         $q,
     // (to be) replaced by "this text" and +text kind of queries
         $bool,
         $sort,
         $author) = xarVarCleanFromInput('startnum',
                                        'cids',
                                        'andcids',
                                        'catid',
                                        'ptid',
                                        'ptids',
                                        'q',
                                        'bool',
                                        'sort',
                                        'author');

// TODO: could we need this someday ?
    if (isset($args['extrainfo'])) {
        extract($args['extrainfo']);
    }

// TODO: clean up this copy & paste stuff :-)

    // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = 20;
    }

    if (!xarModAPILoad('articles', 'user')) return;

    // Get publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    // frontpage or approved status
    $status = array(3,2);

    if (!isset($sort)) {
        $sort = null;
    }

    // default publication type(s) to search in
    if (!empty($ptid)) {
        $ptids = array($ptid);
    } elseif (!isset($ptids)) {
    //    $ptids = array(xarModGetVar('articles','defaultpubtype'));
        $ptids = array();
        foreach ($pubtypes as $pubid => $pubtype) {
            $ptids[] = $pubid;
        }
    }

    $isdefault = 0;
    if (!empty($ptid)) {
        $ptids = array($ptid);
        $settings = unserialize(xarModGetVar('articles', 'settings.'.$ptid));
/*
        // check default view for this type of articles
        if (empty($catid) && empty($cids) && empty($authorid) && empty($sort)) {
            if (substr($settings['defaultview'],0,1) == 'c') {
                $catid = substr($settings['defaultview'],1);
            }
        }
    // Note: 'sort' is used to override the default start view too
        if (substr($settings['defaultview'],0,1) == 'c') {
            if (!isset($sort)) {
                $sort = 'date';
            }
            $isdefault = 1;
        }
*/
        if (empty($settings['showcategories'])) {
            $showcategories = 0;
        } else {
            $showcategories = 1;
        }
        if (empty($settings['showcomments'])) {
            $showcomments = 0;
        } else {
            $showcomments = 1;
        }
    } elseif (!empty($ptids) && count($ptids) > 0) {
        foreach ($ptids as $ptid) {
            // default view doesn't apply here ?!
        }
        $showcategories = 1;
        $showcomments = 0;
    } elseif (!isset($ptids)) {
    //    $ptids = array(xarModGetVar('articles','defaultpubtype'));
        $ptids = array();
        foreach ($pubtypes as $pubid => $pubtype) {
            $ptids[] = $pubid;
        }
    } else {
    // TODO: rethink this when we're dealing with multi-pubtype categories
        $showcategories = 0;
        $showcomments = 0;
    }

    // turn $catid into $cids array (and set $andcids flag)
    if (!empty($catid)) {
        if (strpos($catid,' ')) {
            $cids = explode(' ',$catid);
            $andcids = true;
        } elseif (strpos($catid,'+')) {
            $cids = explode('+',$catid);
            $andcids = true;
        } else {
            $cids = explode('-',$catid);
            $andcids = false;
        }
    }
    $seencid = array();
    $catid = null;
    if (isset($cids) && is_array($cids)) {
        foreach ($cids as $cid) {
            $seencid[$cid] = 1;
        }
        if ($andcids) {
            $catid = join('+',$cids);
        } else {
            $catid = join('-',$cids);
        }
    }
    $seenptid = array();
    if (isset($ptids) && is_array($ptids)) {
        foreach ($ptids as $curptid) {
            $seenptid[$curptid] = 1;
        }
    }

    // Find the uid of the author we're looking for
    if (!empty($author)) {
        // Load API
        if (!xarModAPILoad('roles', 'user')) return;
        $user = xarModAPIFunc('roles','user','get',
                             array('name' => $author));
        if (!empty($user['uid'])) {
            $authorid = $user['uid'];
        } else {
            $authorid = null;
        }
    } else {
        $authorid = null;
    }

    $data = array();
    $data['results'] = array();
    $data['status'] = '';
    if (!empty($q) || (!empty($author) && isset($authorid))) {
        $count = 0;
        $catinfo = array();
        // TODO: allow combination of searches ?
        foreach ($ptids as $curptid) {
            $articles = xarModAPIFunc('articles',
                                     'user',
                                     'getall',
                                     array('startnum' => $startnum,
                                           'cids' => $cids,
                                           'andcids' => $andcids,
                                           'ptid' => $curptid,
                                           'authorid' => $authorid,
                                           'sort' => $sort,
                                           'numitems' => $numitems,
                                           'status' => $status,
                                           'enddate' => time(),
                                           'search' => $q,
                                           'fields' => array('aid','title',
                                                      'pubdate','pubtypeid','cids')
                                          )
                                    );
        // TODO: re-use article output code from elsewhere (view / archive / admin)
            if (!empty($articles) && count($articles) > 0) {

            // TODO: optimize this stuff a little bit...
                if ($showcategories) {
                    // get root categories for this publication type
                    $catlinks = xarModAPIFunc('articles',
                                             'user',
                                             'getrootcats',
                                             array('ptid' => $curptid));
                    // grab the name and link of all children too
                    foreach ($catlinks as $info) {
                        $cattree = xarModAPIFunc('articles',
                                                'user',
                                                'getchildcats',
                                                array('cid' => $info['catid'],
                                                      'ptid' => $curptid,
                                                      // we don't want counts here
                                                      'count' => false));
                        foreach ($cattree as $catitem) {
                            $catinfo[$catitem['id']] = array('name' => $catitem['name'],
                                                             'link' => $catitem['link'],
                                                             'root' => $info['catid']);
                        }
                    }
                    unset($cattree);
                }

                $items = array();
                foreach ($articles as $article) {
                    $count++;
                    $curptid = $article['pubtypeid'];
                    $link = xarModURL('articles','user','display',
                                     array('aid' => $article['aid'],
                                           'ptid' => $article['pubtypeid']));
                    // publication date of article (if needed)
                    if (!empty($pubtypes[$curptid]['config']['pubdate']['label'])
                        && !empty($article['pubdate'])) {
                        $date = strftime('%a, %d %B %Y %H:%M:%S %Z', $article['pubdate']);
                    } else {
                        $date = '';
                    }
                    if (empty($article['title'])) {
                        $article['title'] = xarML('(none)');
                    }

                    // categories this article belongs to
                    $categories = array();
                    if ($showcategories && !empty($article['cids']) &&
                        is_array($article['cids']) && count($article['cids']) > 0) {

                        // order cids by root category (to be improved)
                        $cidlist = $article['cids'];
                        usort($cidlist,'articles_user_sortbyroot');

                        $join = '';
                        foreach ($cidlist as $cid) {
                            $item = array();
                            if (!isset($catinfo[$cid])) {
                                // oops
                                continue;
                            }
                            $categories[] = array('cname' => $catinfo[$cid]['name'],
                                                  'clink' => $catinfo[$cid]['link'],
                                                  'cjoin' => $join);
                            if (empty($join)) {
                                $join = ' | ';
                            }
                        }
                    }

                    $items[] = array('title' => xarVarPrepForDisplay($article['title']),
                                     'link' => $link,
                                     'date' => $date,
                                     'categories' => $categories);
                }
                unset($articles);

                // Pager
// TODO: make count depend on language in the future
                $pager = xarTplGetPager($startnum,
                                        xarModAPIFunc('articles', 'user', 'countitems',
                                                      array('cids' => $cids,
                                                            'andcids' => $andcids,
                                                            'ptid' => $curptid,
                                                            'authorid' => $authorid,
                                                            'status' => $status,
                                                            'enddate' => time(),
                                                            'search' => $q)),

/* trick : use *this* articles search instead of global search for pager :-)
                                        xarModURL('search', 'user', 'main',
*/
                                        xarModURL('articles', 'user', 'search',
                                                  array('catid' => $catid,
                                                        'ptid' => $curptid,
                                                        'author' => $author,
                                                        'sort' => $sort,
                                                        'q' => $q,
                                                        'startnum' => '%%')),
                                        $numitems);

                if (strlen($pager) > 5) {
                    if (!isset($sort) || $sort == 'date') {
                        $othersort = 'title';
                    } else {
                        $othersort = null;
                    }
                    $sortlink = xarModURL('articles',
                                         'user',
                                         'search',
                                         array('catid' => $catid,
                                               'ptid' => $curptid,
                                               'author' => $author,
                                               'sort' => $othersort,
                                               'q' => $q));
                    if (!isset($othersort)) {
                        $othersort = 'date';
                    }
                    $pager .= '&nbsp;&nbsp;<a href="' . $sortlink . '">' .
                              xarML('sort by') . ' ' . xarML($othersort) . '</a>';
                }

                $data['results'][] = array('description' => xarVarPrepForDisplay($pubtypes[$curptid]['descr']),
                                           'items' => $items,
                                           'pager' => $pager);
            }
        }
        unset($articles);
        unset($catinfo);
        unset($items);

        if ($count > 0) {
            // bail out, we have what we needed
            return xarTplModule('articles','user','search',$data);
        }

        $data['status'] = 'Same player shoot again... ;-)';
    }

    if (!xarModAPILoad('categories', 'user')) return;
    if (!xarModAPILoad('categories', 'visual')) return;

/*
    $dump = '';
    $dump .= xarML('Filter') . ' : <select name="ptids[]" multiple><option value=""> ' . xarML('Publication');
    foreach ($pubtypes as $pubid => $pubtype) {
        if ($pubid == $ptid) {
            $dump .= '<option value="' . $pubid . '" selected> - ' . xarVarPrepForDisplay($pubtype['descr']);
        } else {
            $dump .= '<option value="' . $pubid . '"> - ' . xarVarPrepForDisplay($pubtype['descr']);
        }
    }
    $dump .= '</select>';
*/

    $data['publications'] = array();
    foreach ($pubtypes as $pubid => $pubtype) {
        if (!empty($seenptid[$pubid])) {
            $checked = ' checked';
        } else {
            $checked = '';
        }
        $data['publications'][] = array('pubid' => $pubid,
                                        'pubdescr' => xarVarPrepForDisplay($pubtype['descr']),
                                        'pubchecked' => $checked);
    }

    $catarray = array();
    foreach ($ptids as $curptid) {
        // get root categories for this publication type
        $catlinks = xarModAPIFunc('articles',
                                 'user',
                                 'getrootcats',
                                 array('ptid' => $curptid));
        foreach ($catlinks as $cat) {
            $catarray[$cat['catid']] = $cat['cattitle'];
        }
    }

    $data['categories'] = array();
    foreach ($catarray as $cid => $title) {
        $select = xarModAPIFunc('categories',
                                'visual',
                                'makeselect',
                                Array('cid' => $cid,
                                      'return_itself' => false,
                                      'values' => &$seencid,
                                      'multiple' => 1));
        $data['categories'][] = array('cattitle' => $title,
                                      'catselect' => $select);
    }

    return xarTplModule('articles','user','search',$data);
}

/**
 * sorting function for article categories
 */
function articles_user_sortbyroot ($a,$b) {
    global $catinfo;
    if ($catinfo[$a]['root'] == $catinfo[$b]['root']) return 0;
    return ($catinfo[$a]['root'] > $catinfo[$b]['root']) ? -1 : 1;
}

?>
