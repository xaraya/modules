<?php

function xarpages_funcapi_news($args)
{
    // The articles publication type is required, and is selected by the page.
    // Only one publication type is allowed per page.
    if (empty($args['current_page']['dd']['pubtype'])) {
        return $args;
    }

    $ptid = $args['current_page']['dd']['pubtype'];
    $ptids = array($ptid);

    // Parameter to allow selection of only front page articles. Limit to frontpage if true.
    xarVarFetch('frontpage', 'bool', $frontpage, false, XARVAR_NOT_REQUIRED);

    // For the pager
    xarVarFetch('startnum', 'int:1', $startnum, 1, XARVAR_NOT_REQUIRED);
    xarVarFetch('numitems', 'int:1:100', $numitems, 20, XARVAR_NOT_REQUIRED);

    // An individual item has been selected
    xarVarFetch('aid', 'id', $aid, 0, XARVAR_NOT_REQUIRED);

    // A single category selected: cid=N
    xarVarFetch('cid', 'id', $cid, 0, XARVAR_NOT_REQUIRED);
    // A group of categories selected: cids=N[]
    xarVarFetch('cids', 'list:id', $cids, array(), XARVAR_NOT_REQUIRED);

    // TODO: full this in from a parameter.
    $sort = '';

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
    // TODO: filter out any not under the root categories for this page - perhaps?
    //var_dump($cids);

    // Categories are always ANDed.
    $andcids = true;

    // Set the statuses.
    // May extend for the administrator, to show drafts in-situ.
    if ($frontpage) {
        $status = array(3);
    } else {
        $status = array(3, 2);
    }

    // Don't display future items (perhaps unless an administrator?)
    // Take us to the end of today, as an end date.
    $enddate = strtotime(strftime('%d-%b-%Y') . ' +1 day');

    // Get details for all pubtypes
    $pubtypes = xarModAPIFunc('articles', 'user', 'getpubtypes');
    //echo "<pre>"; var_dump($pubtypes); echo "</pre>";

    $article_select = array(
        'startnum' => $startnum,
        'numitems' => $numitems,
        'cids' => $cids,
        'andcids' => $andcids,
        'status' => $status,
        'sort' => $sort,
        //'extra' => $extra,
        //'where' => $where,
        'ptids' => $ptids,
        'ptid' => $ptids,
        'enddate' => $enddate,
        'fields' => array(
            'title', 'aid', 'title', 'summary', 'authorid',
            'pubdate', 'pubtypeid', 'notes', 'status', 'body',
            'dynamicdata',
        ),
    );

    $articles = xarModAPIFunc('articles', 'user', 'getall', $article_select);
    //echo "<pre>"; var_dump($articles); echo "</pre>";

    // If an individual article has been selected, then get that separately.
    $article = array();
    if (!empty($aid)) {
        $single_article_select = $article_select;
        $single_article_select['aid'] = $aid;
        $article = xarModAPIFunc('articles', 'user', 'get', $single_article_select);
        //echo "<pre>"; var_dump($article); echo "</pre>";

        // Do transform hooks.
        // TODO: transform some dynamic data fields too?
        $article['transform'] = array();
        $article['transform'][] = 'summary';
        $article['transform'][] = 'body';
        $article['transform'][] = 'notes';
        $article['itemtype'] = $article['pubtypeid'];
        $article['itemid'] = $article['aid'];
        $article = xarModCallHooks('item', 'transform', $article['aid'], $article, 'articles');

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
                // TODO: change the startnum values if we move across a pager boundary.
                if (count($range_articles) <= 1) {
                    // No next or previous.
                    $next_article = array();
                    $next_url = '';
                    $prev_article = array();
                    $prev_url = '';
                } elseif (count($range_articles) == 2) {
                    if ($article_number == 1) {
                        // No previous (next only)
                        $next_article = array_pop($range_articles);
                        $next_url = xarServerGetCurrentURL(array('aid'=>$next_article['aid']));
                        $prev_article = array();
                        $prev_url = '';
                    } else {
                        // No next (previous only)
                        $next_article = array();
                        $next_url = '';
                        $prev_article = array_shift($range_articles);
                        $prev_url = xarServerGetCurrentURL(array('aid'=>$prev_article['aid']));
                    }
                } elseif (count($range_articles) >= 3) {
                    // Both next and previous
                    $next_article = array_pop($range_articles);
                    $next_url = xarServerGetCurrentURL(array('aid'=>$next_article['aid']));
                    $prev_article = array_shift($range_articles);
                    $prev_url = xarServerGetCurrentURL(array('aid'=>$prev_article['aid']));
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

    // TODO: a pager for long lists of items
    // TODO: an archive (probably not so useful for Bifffaward)
    // TODO: handle categories: default, user-selected
    
    // Return the list of articles.
    $args['article'] = $article;
    $args['articles'] = $articles;
    $args['pubtypes'] = $pubtypes;
    
    return $args;
}

?>
