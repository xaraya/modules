<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * search publications (called as hook from search module, or directly with pager)
 *
 * @param $args['objectid'] could be the query ? (currently unused)
 * @param $args['extrainfo'] all other parameters ? (currently unused)
 * @return array output
 */
function publications_user_search($args)
{
    // pager stuff
    if(!xarVar::fetch('startnum', 'int:0', $startnum,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    // categories stuff
    if(!xarVar::fetch('cids',     'array', $cids,      NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('andcids',  'str',   $andcids,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('catid',    'str',   $catid,     NULL, XARVAR_NOT_REQUIRED)) {return;}

    // single publication type when called via the pager
    if(!xarVar::fetch('ptid',     'id',    $ptid,      NULL, XARVAR_NOT_REQUIRED)) {return;}

    // multiple publication types when called via search hooks
    if(!xarVar::fetch('ptids',    'array', $ptids,     NULL, XARVAR_NOT_REQUIRED)) {return;}

    // date stuff via forms
    if(!xarVar::fetch('publications_startdate','str', $startdate, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('publications_enddate',  'str', $enddate,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    // date stuff via URLs
    if(!xarVar::fetch('start',    'int:0', $start,     NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('end',      'int:0', $end,       NULL, XARVAR_NOT_REQUIRED)) {return;}

    // search button was pressed
    if(!xarVar::fetch('search',   'str',   $search,    NULL, XARVAR_NOT_REQUIRED)) {return;}

    // select by article state (array or string)
    if(!xarVar::fetch('state',   'isset', $state,    NULL, XARVAR_NOT_REQUIRED)) {return;}

    // yes, this is the query
    if(!xarVar::fetch('q',        'str',   $q,         NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('author',   'str',   $author,    NULL, XARVAR_NOT_REQUIRED)) {return;}

    // filter by category
    if(!xarVar::fetch('by',       'str',   $by,     NULL, XARVAR_NOT_REQUIRED)) {return;}

    // can't use list enum here, because we don't know which sorts might be used
    if(!xarVar::fetch('sort', 'regexp:/^[\w,]*$/', $sort, NULL, XARVAR_NOT_REQUIRED)) {return;}

    // boolean AND/OR for words (no longer used)
    //if(!xarVar::fetch('bool',     'str',   $bool,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    // search in specific fields
    if(!xarVar::fetch('publications_fields', 'isset', $fields, NULL, XARVAR_NOT_REQUIRED)) {return;}

    if(!xarVar::fetch('searchtype', 'isset', $searchtype, NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (isset($args['objectid'])) {
        $ishooked = 1;
    } else {
        $ishooked = 0;
        if (empty($fields)) {
            // search in specific fields via URLs
            if(!xarVar::fetch('fields', 'isset', $fields, NULL, XARVAR_NOT_REQUIRED)) {return;}
        }
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

    if (!xarModAPILoad('publications', 'user')) return;

    // Get publication types
    $pubtypes = xarMod::apiFunc('publications','user','get_pubtypes');

    if (xarSecurityCheck('AdminPublications',0)) {
        $isadmin = true;
    } else {
        $isadmin = false;
    }

    // frontpage or approved state
    if (!$isadmin || !isset($state)) {
        $state = array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED);
    } elseif (is_string($state)) {
        if (strpos($state,' ')) {
            $state = explode(' ',$state);
        } elseif (strpos($state,'+')) {
            $state = explode('+',$state);
        } else {
            $state = array($state);
        }
    }
    $seenstate = array();
    foreach ($state as $that) {
        if (empty($that) || !is_numeric($that)) continue;
        $seenstate[$that] = 1;
    }
    $state = array_keys($seenstate);
    if (count($state) != 2 || !in_array(PUBLICATIONS_STATE_APPROVED,$state) || !in_array(PUBLICATIONS_STATE_FRONTPAGE,$state)) {
        $stateline = implode('+',$state);
    } else {
        $stateline = null;
    }

    if (!isset($sort)) {

        $sort = null;
    }

    // default publication type(s) to search in
    if (!empty($ptid) && isset($pubtypes[$ptid])) {
        $ptids = array($ptid);
        $settings = unserialize(xarModVars::get('publications', 'settings.'.$ptid));
        if (empty($settings['show_categories'])) {
            $show_categories = 0;
        } else {
            $show_categories = 1;
        }
    } elseif (!empty($ptids) && count($ptids) > 0) {
        foreach ($ptids as $curptid) {
            // default view doesn't apply here ?!
        }
        $show_categories = 1;
    } elseif (!isset($ptids)) {
    //    $ptids = array(xarModVars::get('publications','defaultpubtype'));
        $ptids = array();
        foreach ($pubtypes as $pubid => $pubtype) {
            $ptids[] = $pubid;
        }
        $show_categories = 1;
    } else {
    // TODO: rethink this when we're dealing with multi-pubtype categories
        $show_categories = 0;
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
            if (empty($cid) || !preg_match('/^_?[0-9]+$/',$cid)) continue;
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
            if (empty($curptid) || !isset($pubtypes[$curptid])) continue;
            $seenptid[$curptid] = 1;
        }
        $ptids = array_keys($seenptid);
    }
    /* Ensure whitespace alone not passed to api -causes errors */
    if (isset($q) && trim($q) === '') {
        $q = null;
    }

    // Find the id of the author we're looking for
    if (!empty($author)) {
        // Load API
        if (!xarModAPILoad('roles', 'user')) return;
        $user = xarMod::apiFunc('roles','user','get',
                             array('name' => $author));
        if (!empty($user['uid'])) {
            $owner = $user['uid'];
        } else {
            $owner = null;
            $author = null;
        }
    } else {
        $owner = null;
        $author = null;
    }

    if (isset($start) && is_numeric($start)) {
        $startdate = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$start);
    }
    if (isset($end) && is_numeric($end)) {
        $enddate = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$end);
    }

    if (empty($fields)) {
        $fieldlist = array('title', 'description', 'summary', 'body1');
    } else {
        $fieldlist = array_keys($fields);
        // don't pass fields via URLs if we stick to the default list
        if (count($fields) == 3 && isset($fields['title']) && isset($fields['description']) && isset($fields['summary']) && isset($fields['body1'])) {
            $fields = null;
        }
    }

    // Set default searchtype to 'fulltext' if necessary
    $fulltext = xarModVars::get('publications', 'fulltextsearch');
    if (!isset($searchtype) && !empty($fulltext)) {
        $searchtype = 'fulltext';
    }
// FIXME: fulltext only supports searching in all configured text fields !
    if (empty($fields) && !empty($fulltext) && !empty($searchtype) && $searchtype == 'fulltext') {
        $fieldlist = explode(',', $fulltext);
    }

    $data = array();
    $data['results'] = array();
    $data['state'] = '';
    $data['ishooked'] = $ishooked;
    // TODO: MichelV: $ishooked is never empty, but either 0 or 1
    if (empty($ishooked)) {
        $data['q'] = isset($q) ? xarVarPrepForDisplay($q) : null;
        $data['author'] = isset($author) ? xarVarPrepForDisplay($author) : null;
        $data['searchtype'] = $searchtype;
    }
    if ($isadmin) {
        $states = xarMod::apiFunc('publications','user','getstates');
        $data['statelist'] = array();
        foreach ($states as $id => $name) {
            $data['statelist'][] = array('id' => $id, 'name' => $name, 'checked' => in_array($id,$state));
        }
    }

    // TODO: show field labels when we're dealing with only 1 pubtype
        $data['fieldlist'] = array(
                                    array('id' => 'title', 'name' => xarML('title'), 'checked' => in_array('title',$fieldlist)),
                                    array('id' => 'description', 'name' => xarML('description'), 'checked' => in_array('description',$fieldlist)),
                                    array('id' => 'summary', 'name' => xarML('summary'), 'checked' => in_array('summary',$fieldlist)),
                                    array('id' => 'body1', 'name' => xarML('body1'), 'checked' => in_array('body1',$fieldlist)),
//                                     array('id' => 'notes', 'name' => xarML('notes'), 'checked' => in_array('notes',$fieldlist)),
                                   );

    $data['publications'] = array();
    foreach ($pubtypes as $pubid => $pubtype) {
        if (!empty($seenptid[$pubid])) {
            $checked = ' checked="checked"';
        } else {
            $checked = '';
        }
        $data['publications'][] = array('id' => $pubid,
                                        'description' => xarVarPrepForDisplay($pubtype['description']),
                                        'checked' => $checked);
    }

    $data['categories'] = array();
    if (!empty($by) && $by == 'cat') {
        $catarray = array();
        foreach ($ptids as $curptid) {
            // get root categories for this publication type
            $catlinks = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'publications','itemtype' => $curptid));
            foreach ($catlinks as $cat) {
                $catarray[$cat['category_id']] = $cat['name'];
            }
        }

        foreach ($catarray as $cid => $title) {
            $select = xarMod::apiFunc('categories',
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
        $data['startdate'] = 'N/A';
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
        $data['startdate'] = $startdate;
    }
    if (empty($enddate)) {
        $enddate = $now;
        $data['enddate'] = 'N/A';
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
        $data['enddate'] = $enddate;
    }

    if (!empty($q) || (!empty($author) && isset($owner)) || !empty($search) || !empty($ptid) || !empty($startdate) || $enddate != $now || !empty($catid)) {
        $getfields = array('id','title', 'start_date','pubtype_id','cids');
        // Return the relevance when using MySQL full-text search
        //if (!empty($search) && !empty($searchtype) && substr($searchtype,0,8) == 'fulltext') {
        //    $getfields[] = 'relevance';
        //}
        $count = 0;
        // TODO: allow combination of searches ?
        foreach ($ptids as $curptid) {
            $publications = xarMod::apiFunc('publications',
                                     'user',
                                     'getall',
                                     array('startnum' => $startnum,
                                           'cids' => $cids,
                                           'andcids' => $andcids,
                                           'ptid' => $curptid,
                                           'owner' => $owner,
                                           'sort' => $sort,
                                           'numitems' => $numitems,
                                           'state' => $state,
                                           'start_date' => $startdate,
                                           'end_date' => $enddate,
                                           'searchfields' => $fieldlist,
                                           'searchtype' => $searchtype,
                                           'search' => $q,
                                           'fields' => $getfields
                                          )
                                    );
        // TODO: re-use article output code from elsewhere (view / archive / admin)
            if (!empty($publications) && count($publications) > 0) {

                // retrieve the categories for each article
                $catinfo = array();
                if ($show_categories) {
                    $cidlist = array();
                    foreach ($publications as $article) {
                        if (!empty($article['cids']) && count($article['cids']) > 0) {
                            foreach ($article['cids'] as $cid) {
                                $cidlist[$cid] = 1;
                            }
                        }
                    }
                    if (count($cidlist) > 0) {
                        $catinfo = xarMod::apiFunc('categories','user','getcatinfo',
                                                 array('cids' => array_keys($cidlist)));
                        // get root categories for this publication type
                        $catroots = xarMod::apiFunc('publications',
                                                  'user',
                                                  'getrootcats',
                                                  array('ptid' => $curptid));
                        $catroots = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'publications','itemtype' => $curptid));

                    }
                    foreach ($catinfo as $cid => $info) {
                        $catinfo[$cid]['name'] = xarVarPrepForDisplay($info['name']);
                        $catinfo[$cid]['link'] = xarModURL('publications','user','view',
                                                           array('ptid' => $curptid,
                                                                 'catid' => (($catid && $andcids) ? $catid . '+' . $cid : $cid) ));
                        // only needed when sorting by root category id
                        $catinfo[$cid]['root'] = 0; // means not found under a root category
                        // only needed when sorting by root category order
                        $catinfo[$cid]['order'] = 0; // means not found under a root category
                        $rootidx = 1;
                        foreach ($catroots as $rootcat) {
                            // see if we're a child category of this rootcat (cfr. Celko model)
                            if ($info['left'] >= $rootcat['left_id'] && $info['left'] < $rootcat['right_id']) {
                                // only needed when sorting by root category id
                                $catinfo[$cid]['root'] = $rootcat['category_id'];
                                // only needed when sorting by root category order
                                $catinfo[$cid]['order'] = $rootidx;
                                break;
                            }
                            $rootidx++;
                        }
                    }

                }

                // needed for sort function below
                $GLOBALS['artsearchcatinfo'] = $catinfo;

                $items = array();
                foreach ($publications as $article) {
                    $count++;
                    $curptid = $article['pubtype_id'];
                    $link = xarModURL('publications','user','display',
                                     array('ptid' => $article['pubtype_id'],
                                           'itemid' => $article['id']));
                    // publication date of article (if needed)
                    if (!empty($pubtypes[$curptid]['config']['startdate']['label'])
                        && !empty($article['startdate'])) {
                        $date = xarLocaleFormatDate('%a, %d %B %Y %H:%M:%S %Z', $article['startdate']);
                        $startdate = $article['startdate'];
                    } else {
                        $date = '';
                        $startdate = 0;
                    }
                    if (empty($article['title'])) {
                        $article['title'] = xarML('(none)');
                    }

                    // categories this article belongs to
                    $categories = array();
                    if ($show_categories && !empty($article['cids']) &&
                        is_array($article['cids']) && count($article['cids']) > 0) {

                        $cidlist = $article['cids'];
                        // order cids by root category order
                        usort($cidlist,'publications_search_sortbyorder');
                        // order cids by root category id
                        //usort($cidlist,'publications_search_sortbyroot');
                        // order cids by position in Celko tree
                        //usort($cidlist,'publications_search_sortbyleft');

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

                    $items[] = array('title' => xarVarPrepHTMLDisplay($article['title']),
                                     'locale' => $article['locale'],

                                     'link' => $link,
                                     'date' => $date,
                                     'startdate' => $startdate,
                                     'relevance' => isset($article['relevance']) ? $article['relevance'] : null,
                                     'categories' => $categories);
                }
                unset($publications);

                // Pager
// TODO: make count depend on locale in the future
                sys::import('modules.base.class.pager');
                $pager = xarTplPager::getPager($startnum,
                                        xarMod::apiFunc('publications', 'user', 'countitems',
                                                      array('cids' => $cids,
                                                            'andcids' => $andcids,
                                                            'ptid' => $curptid,
                                                            'owner' => $owner,
                                                            'state' => $state,
                                                            'startdate' => $startdate,
                                                            'enddate' => $enddate,
                                                            'searchfields' => $fieldlist,
                                                            'searchtype' => $searchtype,
                                                            'search' => $q)),

/* trick : use *this* publications search instead of global search for pager :-)
                                        xarModURL('search', 'user', 'main',
*/
                                        xarModURL('publications', 'user', 'search',
                                                  array('ptid' => $curptid,
                                                        'catid' => $catid,
                                                        'q' => isset($q) ? $q : null,
                                                        'author' => isset($author) ? $author : null,
                                                        'start' => $startdate,
                                                        'end' => ($enddate != $now) ? $enddate : null,
                                                        'state' => $stateline,
                                                        'sort' => $sort,
                                                        'fields' => $fields,
                                                        'searchtype' => !empty($searchtype) ? $searchtype : null,
                                                        'startnum' => '%%')),
                                        $numitems);

                if (strlen($pager) > 5) {
                    if (!isset($sort) || $sort == 'date') {
                        $othersort = 'title';
                    } else {
                        $othersort = 'date';
                    }
                    $sortlink = xarModURL('publications',
                                         'user',
                                         'search',
                                         array('ptid' => $curptid,
                                               'catid' => $catid,
                                               'q' => isset($q) ? $q : null,
                                               'author' => isset($author) ? $author : null,
                                               'start' => $startdate,
                                               'end' => ($enddate != $now) ? $enddate : null,
                                               'state' => $stateline,
                                               'fields' => $fields,
                                               'searchtype' => !empty($searchtype) ? $searchtype : null,
                                               'sort' => $othersort));

                    $pager .= '&#160;&#160;<a href="' . $sortlink . '">' .
                              xarML('sort by') . ' ' . xarML($othersort) . '</a>';
                }

                $data['results'][] = array('description' => xarVarPrepForDisplay($pubtypes[$curptid]['description']),
                                           'items' => $items,
                                           'pager' => $pager);
            }
        }
        unset($catinfo);
        unset($items);
        unset($GLOBALS['artsearchcatinfo']);

        if ($count > 0) {
            // bail out, we have what we needed
            return xarTplModule('publications','user','search',$data);
        }

        $data['state'] = xarML('No pages found matching this search');
    }

    return xarTplModule('publications','user','search',$data);
}

/**
 * sorting function for article categories
 */
function publications_search_sortbyroot ($a,$b)
{
    if ($GLOBALS['artsearchcatinfo'][$a]['root'] == $GLOBALS['artsearchcatinfo'][$b]['root']) {
        return publications_search_sortbyleft($a,$b);
    }
    return ($GLOBALS['artsearchcatinfo'][$a]['root'] > $GLOBALS['artsearchcatinfo'][$b]['root']) ? 1 : -1;
}

function publications_search_sortbyleft ($a,$b)
{
    if ($GLOBALS['artsearchcatinfo'][$a]['left'] == $GLOBALS['artsearchcatinfo'][$b]['left']) return 0;
    return ($GLOBALS['artsearchcatinfo'][$a]['left'] > $GLOBALS['artsearchcatinfo'][$b]['left']) ? 1 : -1;
}

function publications_search_sortbyorder ($a,$b)
{
    if ($GLOBALS['artsearchcatinfo'][$a]['order'] == $GLOBALS['artsearchcatinfo'][$b]['order']) {
        return publications_search_sortbyleft($a,$b);
    }
    return ($GLOBALS['artsearchcatinfo'][$a]['order'] > $GLOBALS['artsearchcatinfo'][$b]['order']) ? 1 : -1;
}

?>