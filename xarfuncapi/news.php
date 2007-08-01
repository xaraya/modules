<?php

// See xarpages/xardocs/news.txt for further details

function xarpages_funcapi_news($args)
{
    // The articles publication type is required, and is selected by the page.
    if (empty($args['current_page']['dd']['pubtype'])) return $args;

    // There may be multiple publication types, as a commad-separated list.
    // The articles API can accept such a list directly.
    $ptid = $args['current_page']['dd']['pubtype'];
    $ptids = explode(',', $ptid);

    // Get the details of the publication type (defaults etc.)

    // We get the first pubtype for historical reasons (DEPRECATED).
    $pubtype = xarModAPIFunc('articles', 'user', 'getpubtypes', array('ptid' => $ptids[0]));

    // Get the global settings if using multiple publication types, otherwise fetch
    // settings for the single selected publication type.
    if (count($ptids) > 1) {
        $settings = unserialize(xarModGetVar('articles', 'settings'));
    } else {
        $settings = unserialize(xarModGetVar('articles', 'settings.' . $ptid));
    }

    // Parameter to allow selection of only front page articles. Limit to frontpage if true.
    xarVarFetch('frontpage', 'bool', $frontpage, false, XARVAR_NOT_REQUIRED);

    // For the pager.
    // Set the max items to be four times the items per page, within a window of 100 to 1000
    // TODO: make this configurable, but these seem reasonable limuts for now.
    $max_numitems_factor = 4;
    $max_numitems_floor = 100;
    $max_numitems_ceiling = 1000;

    $max_numitems = ($settings['itemsperpage'] < ($max_numitems_floor/$max_numitems_factor) ? $max_numitems_floor : ($settings['itemsperpage'] > ($max_numitems_ceiling/$max_numitems_factor) ? $max_numitems_ceiling : $settings['itemsperpage'] * $max_numitems_factor));
    xarVarFetch('startnum', 'int:1', $startnum, 1, XARVAR_NOT_REQUIRED);
    xarVarFetch('numitems', 'int:1:' . $max_numitems, $numitems, $settings['itemsperpage'], XARVAR_NOT_REQUIRED);

    // An individual item has been selected
    // TODO: support selection by name, though this may not be unique enough.
    xarVarFetch('aid', 'id', $aid, 0, XARVAR_NOT_REQUIRED);

    // TODO: support some fancy category joining.
    // A single category selected: cid=N
    xarVarFetch('cid', 'id', $cid, '', XARVAR_NOT_REQUIRED);
    // A group of categories selected: cids=N[]
    xarVarFetch('cids', 'list:id', $cids, array(), XARVAR_NOT_REQUIRED);

    // Keyword search
    xarVarFetch('q', 'pre:trim:passthru:strlist: ,;:pre:lower:trim:passthru:str', $q, NULL, XARVAR_NOT_REQUIRED);
    // Clean up the keywords
    if (!empty($q)) {
        $q = trim(preg_replace('/[^a-z0-9 .,:;@#-]/', '', strtolower($q)));
        $q = trim(preg_replace('/[ ]+/', ' ', $q));
        $q_array = explode(' ', $q);
    }


    // Transform hook details.
    // Start with some defaults, and allow an override.

    // Transform hook fields on summaries.
    $transform_fields_summary = array('summary', 'body', 'notes');

    // Transform hook fields on details.
    $transform_fields_detail = array('summary', 'body', 'notes');

    
    // TODO: allow override using a parameter.
    // General sort methods will be what articles supports (practically just date and title)
    $sort = $settings['defaultsort'];
    
    // Set the URL Params array.
    $url_params = array();

    // Add in some (i.e. all) optional values if they are not set to their defaults.
    if ($startnum > 1) $url_params['startnum'] = $startnum;
    if ($numitems != $settings['itemsperpage']) $url_params['numitems'] = $numitems;
    if (!empty($q)) $url_params['q'] = $q;
    if (!empty($cid)) $url_params['cid'] = $cid;
    if (!empty($cids)) $url_params['cids'] = $cids;
    if (!empty($aid)) $url_params['aid'] = $aid;

    // This flag is set, and passed into the template, if the user is doing any
    // kind of searching, i.e. is not on the first page, is selecting a category
    // or search terms etc.
    $searching_flag = (empty($url_params) ? false : true);

    // Put all the category ids into the cids array.
    if (!empty($cid) && !in_array($cid, $cids)) array_push($cids, $cid);
    
    // The page may have one (or more?) forced categories.
    // This means no matter what additional category the user selects,
    // this category will always be included.
    if (!empty($args['current_page']['dd']['mandatory_cat'])) {
        $mandatory_cat = $args['current_page']['dd']['mandatory_cat'];
        // It should be a comma-separated list
        if (xarVarValidate('strlist:,:id', $mandatory_cat, true)) {
            $mandatory_cats = explode(',', $mandatory_cat);
            foreach($mandatory_cats as $mandatory_cat) {
                array_push($cids, (int)$mandatory_cat);
            }
        }
    }

    // TODO: define base categories and use them for validation, as well as selection.

    // TODO: define select base categories, used to provide links or drop-down lists of categories for the user to select.

    // We should have a list of categories now.

    // Categories are always ANDed.
    $andcids = true;

    // Set the statuses.
    // TOOD: extend for the administrator, to show drafts in-situ.
    if ($frontpage) {
        $status = array(3);
    } else {
        $status = array(3, 2);
    }

    // Don't display future items.
    // TODO: perhaps allow administrators to see more.
    // Take us to the end of today, as an end date.
    $enddate = strtotime(strftime('%d-%b-%Y') . ' +1 day');

    // Get details for all pubtypes
    $pubtypes = xarModAPIFunc('articles', 'user', 'getpubtypes');

    // TODO: Force all categories to be tree-selects.
    // (This appears not to work in the articles getall() API - though it ought to)
    $select_cids = array();
    foreach($cids as $cid_value) {
        $select_cids = preg_replace('/(?<!_)([0-9]+)/', '_$1', $cid_value);
    }

    $article_select = array(
        'startnum' => $startnum,
        'numitems' => $numitems,
        'cids' => $cids,
        'andcids' => $andcids,
        'status' => $status,
        'sort' => $sort,
        'search' => $q,
        //'extra' => $extra,
        //'where' => $where_string,
        //'wheredd' => $wheredd_string,
        //'ptids' => $ptids,
        'ptid' => $ptid,
        'enddate' => $enddate,
        'fields' => array(
            'title', 'aid', 'title', 'summary', 'authorid',
            'pubdate', 'pubtypeid', 'notes', 'status', 'body',
            'dynamicdata', 'cids',
        ),
    );

    $articles = xarModAPIFunc('articles', 'user', 'getall', $article_select);

    // Set the Pager
    $search_count = xarModAPIFunc('articles', 'user', 'countitems', $article_select);
    $pager_url_params = array_merge($url_params, array('pid' => $args['current_page']['pid'], 'startnum' => '%%'));
    $pager_base_url = xarModURL('xarpages', 'user', 'display', $pager_url_params);
    $pager = xarTplGetPager($startnum, $search_count, $pager_base_url, $numitems);

    // If an individual article has been selected, then get that separately.
    $article = array();
    if (!empty($aid)) {
        $single_article_select = $article_select;
        $single_article_select['aid'] = $aid;
        unset($single_article_select['startnum']);
        $article = xarModAPIFunc('articles', 'user', 'get', $single_article_select);

        // Do transform hooks.
        // TODO: transform some dynamic data fields too? Make configurable.
        // If the article does not exist, then skip this section.
        if (!empty($article)) {
            // Only do the transform if there are some fields we want to transform.
            if (!empty($transform_fields_detail)) {
                $article['transform'] = $transform_fields_detail;
                $article['itemtype'] = $article['pubtypeid'];
                $article['itemid'] = $article['aid'];
                $article = xarModCallHooks('item', 'transform', $article['aid'], $article, 'articles');
            }

            // If the article is in the list of articles, then we can provide links
            // to next/previous and other articles.
            $i = 0;
            foreach($articles as $item) {
                if ($item['aid'] == $aid) {
                    // This is the one.
                    // Easiest way is to fetch three articles and get their IDs.

                    // Determine this article ID in the complete list.
                    $article_number = $startnum + $i;

                    $range_article_select = $article_select;

                    // We could be right at the start, or part way through, or right at the end.
                    if ($article_number == 1) {
                        // We are right at the start, so only fetch the next item.
                        $range_article_select['startnum'] = 1;
                        $range_article_select['numitems'] = 2;
                    } else {
                        // Not at the start, so fetch previous/current/next items
                        $range_article_select['startnum'] = $article_number - 1;
                        $range_article_select['numitems'] = 3;
                    }
                    // Fetch the range of articles either side of the current article.
                    $range_articles = xarModAPIFunc('articles', 'user', 'getall', $range_article_select);

                    // Only one article available.
                    if (count($range_articles) <= 1) {
                        // No next or previous.
                        $next_article = array();
                        $next_url = '';
                        $prev_article = array();
                        $prev_url = '';
                    } elseif (count($range_articles) == 2) {
                        if ($article_number == 1) {
                            // No previous (next only)
                            $next_startnum = $startnum;
                            if (($i + 1) == $numitems) $next_startnum = $startnum + $numitems;
                            $next_article = array_pop($range_articles);
                            $next_url = xarServerGetCurrentURL(array('aid'=>$next_article['aid'], 'startnum' => $next_startnum));
                            $prev_article = array();
                            $prev_url = '';
                        } else {
                            // No next (previous only)
                            $next_article = array();
                            $next_url = '';
                            $prev_startnum = $startnum;
                            if ($i == 0) $prev_startnum = $startnum - $numitems;
                            $prev_article = array_shift($range_articles);
                            $prev_url = xarServerGetCurrentURL(array('aid'=>$prev_article['aid'], 'startnum' => $prev_startnum));
                        }
                    } elseif (count($range_articles) >= 3) {
                        // Both next and previous
                        $next_startnum = $startnum;
                        $prev_startnum = $startnum;
                        if (($i + 1) == $numitems) $next_startnum = $startnum + $numitems;
                        if ($i == 0) $prev_startnum = $startnum - $numitems;
                        $next_article = array_pop($range_articles);
                        $next_url = xarServerGetCurrentURL(array('aid'=>$next_article['aid'], 'startnum' => $next_startnum));
                        $prev_article = array_shift($range_articles);
                        $prev_url = xarServerGetCurrentURL(array('aid'=>$prev_article['aid'], 'startnum' => $prev_startnum));
                    }

                    $article['next_article'] = $next_article;
                    $article['next_url'] = $next_url;
                    $article['prev_article'] = $prev_article;
                    $article['prev_url'] = $prev_url;

                    break;
                }
                $i += 1;
            }
        }
    } else {
        // Summary listing.
        // Apply required transform hooks to summaries.
        // Only do the transform if there are some fields we want to transform.
        if (!empty($transform_fields_summary)) {
            foreach($articles as $t_key => $t_article) {
                $t_article['transform'] = $transform_fields_summary;
                $t_article['itemtype'] = $article['pubtypeid'];
                $t_article['itemid'] = $article['aid'];
                $articles[$t_key] = xarModCallHooks('item', 'transform', $t_article['aid'], $t_article, 'articles');
            }
        }
    }

    // TODO: an archive by date - do the summaries here, but only if requested (by parameter or page flag)
    // TODO: handle categories: default, user-selected

    // Return the list of articles.
    $args['article'] = $article;
    $args['articles'] = $articles;
    $args['pubtype'] = $pubtype; // Deprecated
    $args['pubtypes'] = $pubtypes;

    // Return data for template use
    $args['extra'] = array(
        'url_params' => $url_params,
        'pager' => $pager,
        'search_count' => $search_count,
        'searching_flag' => $searching_flag,
        'aid' => $aid,
    );

    return $args;
}

// Get counts of articles for each year and month to enable
// and arthive menu to be provided.

function xarpages_funcapi_news_archive($args)
{
}

?>
