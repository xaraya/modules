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
     // date stuff via forms
         $startdate,
         $enddate,
     // date stuff via URLs
         $start,
         $end,
     // search button was pressed
         $search,
     // select by article status
         $status,
     // yes, this is the query
         $q,
     // (to be) replaced by "this text" and +text kind of queries
         $bool,
         $sort,
         $by,
         $author) = xarVarCleanFromInput('startnum',
                                        'cids',
                                        'andcids',
                                        'catid',
                                        'ptid',
                                        'ptids',
                                        'articles_startdate',
                                        'articles_enddate',
                                        'start',
                                        'end',
                                        'search',
                                        'status',
                                        'q',
                                        'bool',
                                        'sort',
                                        'by',
                                        'author');

    if (isset($args['objectid'])) {
        $ishooked = 1;
    } else {
        $ishooked = 0;
    }

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

    if (xarSecurityCheck('AdminArticles',0)) {
        $isadmin = true;
    } else {
        $isadmin = false;
    }

    // frontpage or approved status
    if (!$isadmin || !isset($status)) {
        $status = array(3,2);
    } elseif (is_string($status)) {
        if (strpos($status,' ')) {
            $status = explode(' ',$status);
        } elseif (strpos($status,'+')) {
            $status = explode('+',$status);
        } else {
            $status = array($status);
        }
    }
    if (count($status) != 2 || !in_array(2,$status) || !in_array(3,$status)) {
        $statusline = implode('+',$status);
    } else {
        $statusline = null;
    }

    if (!isset($sort)) {
        $sort = null;
    }

    // default publication type(s) to search in
    if (!empty($ptid)) {
        $ptids = array($ptid);
        $settings = unserialize(xarModGetVar('articles', 'settings.'.$ptid));
        if (empty($settings['showcategories'])) {
            $showcategories = 0;
        } else {
            $showcategories = 1;
        }
    } elseif (!empty($ptids) && count($ptids) > 0) {
        foreach ($ptids as $curptid) {
            // default view doesn't apply here ?!
        }
        $showcategories = 1;
    } elseif (!isset($ptids)) {
    //    $ptids = array(xarModGetVar('articles','defaultpubtype'));
        $ptids = array();
        foreach ($pubtypes as $pubid => $pubtype) {
            $ptids[] = $pubid;
        }
        $showcategories = 1;
    } else {
    // TODO: rethink this when we're dealing with multi-pubtype categories
        $showcategories = 0;
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
            if (empty($cid)) continue;
            $seencid[$cid] = 1;
        }
        $cids = array_keys($seencid);
        if ($andcids) {
            $catid = join('+',$cids);
        } else {
            $catid = join('-',$cids);
        }
    }
    $seenptid = array();
    if (isset($ptids) && is_array($ptids)) {
        foreach ($ptids as $curptid) {
            if (empty($curptid)) continue;
            $seenptid[$curptid] = 1;
        }
        $ptids = array_keys($seenptid);
    }

    if (isset($q) && $q === '') {
        $q = null;
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
            $author = null;
        }
    } else {
        $authorid = null;
        $author = null;
    }

    if (isset($start) && is_numeric($start)) {
        $startdate = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$start);
    }
    if (isset($end) && is_numeric($end)) {
        $enddate = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$end);
    }

    $data = array();
    $data['startdate'] = !empty($startdate) ? $startdate : 'N/A';
    $data['enddate'] = !empty($enddate) ? $enddate : 'N/A';
    $data['results'] = array();
    $data['status'] = '';
    $data['ishooked'] = $ishooked;
    if (empty($ishooked)) {
        $data['q'] = isset($q) ? xarVarPrepForDisplay($q) : null;
        $data['author'] = isset($author) ? xarVarPrepForDisplay($author) : null;
    }
    if ($isadmin) {
        $data['statuslist'] = array(
                                    array('id' => 0, 'name' => xarML('Submitted'), 'checked' => in_array(0,$status)),
                                    array('id' => 1, 'name' => xarML('Rejected'), 'checked' => in_array(1,$status)),
                                    array('id' => 2, 'name' => xarML('Approved'), 'checked' => in_array(2,$status)),
                                    array('id' => 3, 'name' => xarML('Front Page'), 'checked' => in_array(3,$status)),
                                   );
    }

    $data['publications'] = array();
    foreach ($pubtypes as $pubid => $pubtype) {
        if (!empty($seenptid[$pubid])) {
            $checked = ' checked="checked"';
        } else {
            $checked = '';
        }
        $data['publications'][] = array('pubid' => $pubid,
                                        'pubdescr' => xarVarPrepForDisplay($pubtype['descr']),
                                        'pubchecked' => $checked);
    }

    $data['categories'] = array();
    if (!empty($by) && $by == 'cat') {
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

        foreach ($catarray as $cid => $title) {
            $select = xarModAPIFunc('categories',
                                    'visual',
                                    'makeselect',
                                    Array('cid' => $cid,
                                          'return_itself' => true,
                                          'select_itself' => true,
                                          'values' => &$seencid,
                                          'multiple' => 1));
            $data['categories'][] = array('cattitle' => $title,
                                          'catselect' => $select);
        }
        $data['searchurl'] = xarModURL('search','user','main');
    } else {
        $data['searchurl'] = xarModURL('search','user','main',
                                       array('by' => 'cat'));
    }

    $now = time();
    if (empty($startdate)) {
        $startdate = null;
    } else {
        if (!preg_match('/[a-zA-Z]+/',$startdate)) {
            $startdate .= ' GMT';
        }
        $startdate = strtotime($startdate);
        // adjust for the user's timezone offset
        $startdate -= xarMLS_userOffset() * 3600;
        if ($startdate > $now && !$isadmin) {
            $startdate = $now;
        }
    }
    if (empty($enddate)) {
        $enddate = $now;
    } else {
        if (!preg_match('/[a-zA-Z]+/',$enddate)) {
            $enddate .= ' GMT';
        }
        $enddate = strtotime($enddate);
        // adjust for the user's timezone offset
        $enddate -= xarMLS_userOffset() * 3600;
        if ($enddate > $now && !$isadmin) {
            $enddate = $now;
        }
    }

    if (!empty($q) || (!empty($author) && isset($authorid)) || !empty($search) || !empty($ptid) || !empty($startdate) || $enddate != $now || !empty($catid)) {
        $count = 0;
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
                                           'startdate' => $startdate,
                                           'enddate' => $enddate,
                                           'search' => $q,
                                           'fields' => array('aid','title',
                                                      'pubdate','pubtypeid','cids')
                                          )
                                    );
        // TODO: re-use article output code from elsewhere (view / archive / admin)
            if (!empty($articles) && count($articles) > 0) {

                // retrieve the categories for each article
                $catinfo = array();
                if ($showcategories) {
                    $cidlist = array();
                    foreach ($articles as $article) {
                        if (!empty($article['cids']) && count($article['cids']) > 0) {
                            foreach ($article['cids'] as $cid) {
                                $cidlist[$cid] = 1;
                            }
                        }
                    }
                    if (count($cidlist) > 0) {
                        $catinfo = xarModAPIFunc('categories','user','getcatinfo',
                                                 array('cids' => array_keys($cidlist)));
                        // get root categories for this publication type
                        $catroots = xarModAPIFunc('articles',
                                                  'user',
                                                  'getrootcats',
                                                  array('ptid' => $curptid));

                    }
                    foreach ($catinfo as $cid => $info) {
                        $catinfo[$cid]['name'] = xarVarPrepForDisplay($info['name']);
                        $catinfo[$cid]['link'] = xarModURL('articles','user','view',
                                                           array('ptid' => $curptid,
                                                                 'catid' => (($catid && $andcids) ? $catid . '+' . $cid : $cid) ));
                        // only needed when sorting by root id
                        $catinfo[$cid]['root'] = $info['left'];
                        foreach ($catroots as $rootcat) {
                            // see if we're a child category of this rootcat (cfr. Celko model)
                            if ($info['left'] >= $rootcat['catleft'] && $info['left'] < $rootcat['catright']) {
                                $catinfo[$cid]['root'] = $rootcat['catid'];
                                break;
                            }
                        }
                    }

                }

                // needed for sort function below
                $GLOBALS['artsearchcatinfo'] = $catinfo;

                $items = array();
                foreach ($articles as $article) {
                    $count++;
                    $curptid = $article['pubtypeid'];
                    $link = xarModURL('articles','user','display',
                                     array('ptid' => $article['pubtypeid'],
                                           'aid' => $article['aid']));
                    // publication date of article (if needed)
                    if (!empty($pubtypes[$curptid]['config']['pubdate']['label'])
                        && !empty($article['pubdate'])) {
                        $date = xarLocaleFormatDate('%a, %d %B %Y %H:%M:%S %Z', $article['pubdate']);
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

                        $cidlist = $article['cids'];
                        // order cids by root category (to be improved)
                        usort($cidlist,'articles_search_sortbyroot');
                        // order cids by position in Celko tree
                        //usort($cidlist,'articles_search_sortbyleft');

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
                                                            'startdate' => $startdate,
                                                            'enddate' => $enddate,
                                                            'search' => $q)),

/* trick : use *this* articles search instead of global search for pager :-)
                                        xarModURL('search', 'user', 'main',
*/
                                        xarModURL('articles', 'user', 'search',
                                                  array('ptid' => $curptid,
                                                        'catid' => $catid,
                                                        'q' => isset($q) ? urlencode($q) : null,
                                                        'author' => isset($author) ? urlencode($author) : null,
                                                        'start' => $startdate,
                                                        'end' => ($enddate != $now) ? $enddate : null,
                                                        'status' => $statusline,
                                                        'sort' => $sort,
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
                                         array('ptid' => $curptid,
                                               'catid' => $catid,
                                               'q' => isset($q) ? urlencode($q) : null,
                                               'author' => isset($author) ? urlencode($author) : null,
                                               'start' => $startdate,
                                               'end' => ($enddate != $now) ? $enddate : null,
                                               'status' => $statusline,
                                               'sort' => $othersort));
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
        unset($catinfo);
        unset($items);
        unset($GLOBALS['artsearchcatinfo']);

        if ($count > 0) {
            // bail out, we have what we needed
            return xarTplModule('articles','user','search',$data);
        }

        $data['status'] = xarML('No articles found matching this search');
    }

    return xarTplModule('articles','user','search',$data);
}

/**
 * sorting function for article categories
 */
function articles_search_sortbyroot ($a,$b)
{
    if ($GLOBALS['artsearchcatinfo'][$a]['root'] == $GLOBALS['artsearchcatinfo'][$b]['root']) return 0;
    return ($GLOBALS['artsearchcatinfo'][$a]['root'] > $GLOBALS['artsearchcatinfo'][$b]['root']) ? 1 : -1;
}

function articles_search_sortbyleft ($a,$b)
{
    if ($GLOBALS['artsearchcatinfo'][$a]['left'] == $GLOBALS['artsearchcatinfo'][$b]['left']) return 0;
    return ($GLOBALS['artsearchcatinfo'][$a]['left'] > $GLOBALS['artsearchcatinfo'][$b]['left']) ? 1 : -1;
}

?>
