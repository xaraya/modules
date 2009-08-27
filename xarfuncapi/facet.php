<?php

/**
 * Function to provide faceted navigation on items in a page.
 * 
 * This requires a small mod to the core categories module (see readme for details).
 *
 * Templates for the summary and detail of the articles are facet-summary[-pubtypename].xt
 * and facet-display[-pubtypename].xt
 *
 */

function xarpages_funcapi_facet($args)
{
    // The facets array.
    // Each facet contains:
    //    root - the root category (hooked to the item type).
    //    base - the current base category - either the root or the filter category.
    //    filter - the filter category (optional - may not be set).
    //    filter_ancestors - ancestor categories for the filter
    //    counts - a tree of categories under the base, with filtered item counts.
    //    inverted - an inverted category list, used for looking up categories.
    // Facets are indexed by the root category id
    // They are static so we can keep coming back to them while generating URLs.
    static $facets = array();

    // Current filter, cids indexed by root cid (i.e. the facet ID).
    static $filter_cids = array();

    // Inverted category/facet ID lookup. Gives the facet root for every category.
    static $cids_to_facets = array();

    //
    // URL mode. Return existing facet filters as a URL 'filter' parameter string.
    // 'add=cid' or 'remove=cid' or 'show'
    // We need an inverted list of all categories, linking them to the facets (this
    // is the cids_to_factes list).
    // We already have the 'filter_cids' with all the current filter values.
    //
    
    if (!empty($args['add']) || !empty($args['remove']) || !empty($args['show'])) {
        $filter = $filter_cids;

        // Manipulate the list if 'add' or 'remove'.
        if (!empty($args['add']) && is_numeric($args['add'])) {
            if (!empty($cids_to_facets[$args['add']])) {
                $filter[$cids_to_facets[$args['add']]] = $args['add'];
            }
        }

        if (!empty($args['remove']) && is_numeric($args['remove'])) {
            if (!empty($cids_to_facets[$args['remove']])) {
                unset($filter[$cids_to_facets[$args['remove']]]);
            } elseif (!empty($filter[$args['remove']])) {
                unset($filter[$args['remove']]);
            }
        }

        // Return a NULL if there are no filter terms. This will ensure the filter
        // parameter does not appear in the URL.
        return (empty($filter) ? NULL : implode(',', $filter));
    }

    // The publication types (in articles) for the items we are dealing with.
    // Fetch this from the page DD property 'publication_type'.
    // Multiple publication types are separated by commas.
    // This all assumes the articles we are searching for all reside in the articles
    // module. Content in Xaraya is not generic enough to be able to fetch across a
    // range of modules without listing the APIs for each individually.
    if (!empty($args['current_page']['dd']['publication_type'])) {
        $ptids = explode(',', $args['current_page']['dd']['publication_type']);
    } else {
        // Send the error back to the template.
        $args['error'] = xarML('No publication type(s) selected');
        return $args;
    }

    // Module ID
    $modid = xarModGetIDfromName('articles');

    // List of facets for which there are filters in operation.
    $filter_facets = array();

    // List of facets for which there are sub-categories that can be selected.
    $subcat_facets = array();

    // List of pubtypes for which we have edit privileges.
    $edit_privs = array();

    // TODO: also determine any category bases that we specifically want to exclude from the navigation.
    // They may be there for categorisation in other ways.
    // Get the list of the page defination and use that list to strip out any categories we uses as facet roots.

    ////////////////////////////////
    // For each publication type, get the category bases.
    // These will be the facets.
    // For now assume there is no overlap.
    // TODO: deal with overlaps by removing those bases that are descendants of other bases. This will not
    // noramally happen within a single publication type, but may happen with multiple publication types 
    // when one type has a base category that happens to be lower in the tree of a base category in one
    // of the other publication types. Careful selection of hhoked categories can avoid this.
    $global_edit_privs = NULL;
    foreach($ptids as $ptid_key => $ptid) {
        if (xarSecurityCheck('ViewArticles', 0, 'Article', "$ptid:All:All:All")) {
            // Check if we have edit privs.
            if (!isset($edit_privs[$ptid])) {
                $edit_privs[$ptid] = (xarSecurityCheck('EditArticles', 0, 'Article', "$ptid:All:All:All") ? true : false);

                // Update the global (all pubtypes) edit privs flag. true=edit privs on everything, false=some edit privs missing.
                if (!isset($global_edit_privs) || $global_edit_privs == true) $global_edit_privs = $edit_privs[$ptid];
            }

            $itemtype_base_cids_count = xarModAPIfunc(
                'categories', 'user', 'countcatbases',
                array('module' => 'articles', 'itemtype' => $ptid)
            );

            if ($itemtype_base_cids_count > 0) {
                for($i = 1; $i <= $itemtype_base_cids_count; $i++) {
                    $itemtype_base_cid = xarModAPIfunc(
                        'categories', 'user', 'getcatbase',
                        array('modid' => $modid, 'itemtype' => $ptid, 'bid' => $i)
                    );

                    // If we already have this facet, perhaps from a different publication type, then skip it.
                    if (isset($facets[$itemtype_base_cid['cid']])) continue;

                    // TODO: security check here?
                    $facets[$itemtype_base_cid['cid']] = array();
                    $facets[$itemtype_base_cid['cid']]['root'] = (int)$itemtype_base_cid['cid'];
                    $facets[$itemtype_base_cid['cid']]['base'] = $facets[$itemtype_base_cid['cid']]['root'];
                }
            }
        } else {
            unset($ptids[$ptid_key]);
        }
    }

    // Statuses that are displayed.
    // Depending on privileges, additional statuses are included. e.g. 'submitted' for admin.
    // We will probably need to check the privileges on ALL the publication types here, and only
    // allow draft articles through if privileges are high enough on all those publication types.
    if (!empty($global_edit_privs)) {
        // We have edit privs on all these pubtypes, so show the draft (aka submitted) articles too.
        // 1 = rejected (we don't want those)
        $status_list = array(0, 2, 3);
    } else {
        // Default is 'Posted'.
        $status_list = array(2, 3);
    }

    ////////////////////////////////
    // Get input parameters.
    //

    // Get the filter category IDs.
    xarVarFetch('filter', 'strlist:,+ :id', $filter, array(), XARVAR_NOT_REQUIRED);
    if (!is_array($filter)) $filter = explode(',', $filter);

    // Fetch the query string (keywords)
    // TODO: validate and split up the words (ie. sanitize).
    xarVarFetch('q', 'str:1:100', $q, '', XARVAR_NOT_REQUIRED);

    // If a query string is passed in, then do some cleaning first.
    // When using a query string, the way the categories are handled is slightly different
    // i.e. no categories supplied means "any category".
    if (!empty($q)) {
        $args['q'] = $q;
    } else {
        $args['q'] = '';
    }

    // Fetch the article ID.
    xarVarFetch('aid', 'id', $aid, 0, XARVAR_NOT_REQUIRED);

    // Pager 
    $default_numitems = 20;
    xarVarFetch('startnum', 'id', $startnum, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('numitems', 'enum:10:20:50:100', $numitems, $default_numitems, XARVAR_NOT_REQUIRED);

    ////////////////////////////////
    // Place the filter categories into their relevant factets.
    // This includes validation of the filters and fetching their ancestor paths.
    //
    
    // Get the crumbtrail lists for each selected category, i.e. filters already in place.
    //$cids_crumbs = array();
    if (!empty($filter)) {
        foreach($filter as $filter_cid) {
            // Get the ancestors for the filter category, right up to the root category.
            // We may need to trim off some of the root categories, because the facets may start
            // a little higher up.
            $ancestors = xarModAPIfunc(
                'categories', 'user', 'getancestors',
                array('cid' => $filter_cid, 'return_itself' => true, 'order' => 'root')
            );

            if (!empty($ancestors)) {
                // First find the facet that this filter category refers to.
                while(!empty($ancestors)) {
                    // Take the first ancestor.
                    $ancestor = reset($ancestors);
                    if (isset($facets[$ancestor['cid']])) {
                        // Found a match. Store it and break out of the loop.
                        $facet_match = $ancestor['cid'];
                        $facets[$facet_match]['filter'] = $filter_cid;
                        $facets[$facet_match]['base'] = $filter_cid;
                        $facets[$facet_match]['filter_ancestors'] = $ancestors;

                        // Add this facet to the list of those with active filters.
                        $filter_facets[] = $ancestor['cid'];
                        break;
                    } else {
                        // No match. Lose this category and move on to the next one.
                        array_shift($ancestors);
                    }
                    // If we get here and no match was found, then the filter category is
                    // quietly discarded.
                }

                // If we have a crumb trail, make sure the categories are in the reverse index.
                if (!empty($ancestors)) {
                    foreach($ancestors as $ancestor) {
                        $cids_to_facets[$ancestor['cid']] = $facet_match;
                    }
                }
            }
            //echo "<pre>Ancestors for $filter_cid:<br />" . print_r($ancestors, true) . "</pre>";
        }
    }
    //$args['cids_crumbs'] = $cids_crumbs;

    if (!empty($facets)) {
        // Get the filter cids from the current facets.
        //$filter_cids = array(); // defined as static
        foreach($facets as $facet) {
            if (isset($facet['filter'])) $filter_cids[$facet['root']] = $facet['filter'];
        }
    }

    ////////////////////////////////
    // Get the articles fetch array, i.e. the articles selection parameters.
    // This is used both when fetching articles, and when doing the counts.
    //

    if (!empty($facets)) {
        $articles_fetch_array = array();

        if (!empty($aid)) {
            // aid searching overrides all the other criteria.
            $articles_fetch_array = array(
                'aids' => array($aid),
                // Restricted list for the summaries.
            );
        } elseif (!empty($filter_cids) || !empty($q) || !empty($latest)) {
            $articles_fetch_array = array(
                'cids' => (!empty($filter_cids) ? explode(',', '_' . implode(',_', $filter_cids)) : NULL),
                'andcids' => true,
                'search' => $q,
            );

            if (!empty($latest)) {
                // Fetch stuff posted in the last N days.
                $articles_fetch_array['startdate'] = strtotime('today - ' . $latest . ' days');
            }
        }

        // Need to specify search fields as notes are not included by default.
        $articles_fetch_array['searchfields'] = array('aid', 'title', 'summary', 'body', 'notes');

        $articles_fetch_array['fields'] = array('title', 'summary', 'body', 'notes', 'status', 'cids', 'dynamicdata');
        $articles_fetch_array['ptid'] = $ptids;
        $articles_fetch_array['status'] = $status_list;
    }


    ////////////////////////////////
    // Get the facet counts.
    // Now, there is a bug in this. As the counts are summed up towards the root of
    // the categories tree, they can be double-counted. This is due to the way the
    // counts are calculated in the categories module. They need to be done slightly
    // differently at the database level, with no summing up at the PHP level.
    // As it stands, some of the category counts are over-stated, but the visible
    // categories are correct.
    //

    if (!empty($facets)) {
        // Get the base cids.
        $base_cids = array();
        foreach($facets as $facet) {
            $base_cids[] = $facet['base'];
        }
        //echo "Filter=" . '_' . implode('+_', $filter_cids);

        // TODO: need to go via the articles module so that the query string can be included.
        // TODO: need to include filter.
        // 'OR' the base cids, since we want counts under *any* of these categories.
        $deepcount_params = array(
            'modid' => $modid,
            'itemtype' => $ptids,
            'groupby' => 'category',
            'catid' => '_' . implode('-_', $base_cids),
        );

        // If the filter is active, then send the filter query to the deep count too.
        // Link to the articles table here too, so we can apply the query-string
        // filter too.
        // Not only that, it also must include statuses etc.
        // This will only work on MySQL 5+
        if (!empty($filter_cids)) {
            $filter_params = array(
                'modid' => $modid,
                'itemtype' => $ptids,
                'groupby' => 'category',
                'catid' => '_' . implode('+_', $filter_cids),
            );

            // Fetch the categories query parts.
            $catfilterdef = xarModAPIFunc('categories', 'user', 'leftjoin', $filter_params);

            // Fetch the articles query parts.
            // This function does not select for categories, so we add that in ourselves.
            $artfilterdef = xarModAPIFunc('articles', 'user', 'leftjoin', $articles_fetch_array);
            //echo "<pre>Articles def:<br />"; var_dump($artfilterdef); echo "</pre>";

            $catfilterdef['where'] = (empty($catfilterdef['where']) ? $artfilterdef['where'] : $catfilterdef['where'] . ' AND ' . $artfilterdef['where']);

            // Join it all together (i.e. the category and articles queries) into a single query.
            $filter_sql = 'SELECT DISTINCT ' . $catfilterdef['iid']
                . ' FROM ' . $catfilterdef['table']
                . ' INNER JOIN ' . $artfilterdef['table'] . ' ON ' . $artfilterdef['field'] . ' = ' . $catfilterdef['iid']
                . $catfilterdef['more']
                . (!empty($catfilterdef['where']) ? ' WHERE ' . $catfilterdef['where'] : '')
                . ' GROUP BY ' . $catfilterdef['iid'];

            // FIXME: this is a horrible hack necessary to make MySQL work efficiently.
            // The details are here: http://bugs.mysql.com/bug.php?id=4040 and apply up to *at least* MySQL 5.0.45
            // Basically, MySQL transforms "WHERE x IN (SELECT ...)" into "WHERE EXISTS (SELECT ... WHERE x = ...)"
            // For small datasets this is fine, but it slows down exponentially as the number of items increases.
            // With 5000 articles, I was getting query times of 30+ seconds with the above query, and tens of mS
            // with this work-around.
            // What a FAF!
            $filter_sql = 'SELECT filtersub.article_id FROM (' 
                . 'SELECT ' . 'COUNT('.$catfilterdef['iid'].') AS cnt, ' . $catfilterdef['iid'] . ' AS article_id'
                . ' FROM ' . $catfilterdef['table']
                . ' INNER JOIN ' . $artfilterdef['table'] . ' ON ' . $artfilterdef['field'] . ' = ' . $catfilterdef['iid']
                . $catfilterdef['more']
                . (!empty($catfilterdef['where']) ? ' WHERE ' . $catfilterdef['where'] : '')
                . ' GROUP BY ' . $catfilterdef['iid'] . ') AS filtersub WHERE filtersub.cnt = 1';


            // Now, this is an important parameter. This is where the item filter is injected into
            // the 'deepcount' API, restricting the categories counted to just those matching
            // articles in this query. See the readme on the facets functionality for details of
            // what may need changing, assuming it does not get integrated into the core categories module.
            $deepcount_params['iidfilter'] = $filter_sql;
        } else {
            // No categories, but we still need to filter on the articles (status, query string etc)
            // in order to get the counts right.

            // Fetch the articles query parts.
            $artfilterdef = xarModAPIFunc('articles', 'user', 'leftjoin', $articles_fetch_array);

            $filter_sql = 'SELECT ' . $artfilterdef['field']
                . ' FROM ' . $artfilterdef['table']
                . (!empty($artfilterdef['where']) ? ' WHERE ' . $artfilterdef['where'] : '');

            $deepcount_params['iidfilter'] = $filter_sql;
        }

        $deep_counts = xarModAPIfunc('categories', 'user', 'deepcount', $deepcount_params);

        //echo "<pre>Deep Counts for catid=".'_' . implode('-_', $base_cids).":<br />"; var_dump($deep_counts); echo "</pre>";

        // Get the category trees to put the counts onto.
        // We only need the trees from the base category IDs (i.e. the root or the filter for each facet).
        foreach($facets as $facet_key => $facet) {
            $facet_cats = xarModAPIfunc(
                'categories', 'user', 'getcat',
                array(
                    'cid' => $facet['base'],
                    'return_itself' => true,
                    // Depth is < 4, i.e. three levels returned: current, children and their children.
                    // FIXME: actually depth is relative to the ultimate base cids, and not the cid we start at.
                    //'maximum_depth' => 4,
                    'getchildren' => true,
                    'indexby' => 'cid'
                )
            );
            //var_dump($facet_cats);

            // Merge each of these trees with the deep_counts to give us the current available facet trees.
            if (!empty($facet_cats)) {
                // Start by putting the counts onto the category tree.
                foreach($deep_counts as $deep_count_cid => $deep_count_value) {
                    if (isset($facet_cats[$deep_count_cid])) $facet_cats[$deep_count_cid]['count'] = $deep_count_value;
                }

                // Now remove categories with a count of zero.
                $level_offset = -1;
                foreach($facet_cats as $facet_cat_key => $facet_cat_value) {
                    if (empty($facet_cat_value['count'])) {
                        unset($facet_cats[$facet_cat_key]);
                    } else {
                        // Maintain the level, which starts at 0 for the base category.
                        // The 'indentation' could start at any arbitrary level, so get the offset from the first one.
                        if ($level_offset == -1) $level_offset = $facet_cat_value['indentation'];
                        $facet_cats[$facet_cat_key]['level'] = $facet_cat_value['indentation'] - $level_offset;

                        // Also maintain a count of children for each category.
                        // This happens to be a convenient place to do it.
                        if (!empty($facet_cat_value['parent']) && isset($facet_cats[$facet_cat_value['parent']])) {
                            if (empty($facet_cats[$facet_cat_value['parent']]['children'])) {
                                $facet_cats[$facet_cat_value['parent']]['children'] = 1;
                            } else {
                                $facet_cats[$facet_cat_value['parent']]['children'] += 1;
                            }
                        }

                        // Put the category into the inverted index.
                        $cids_to_facets[$facet_cat_value['cid']] = $facet_key;
                    }
                }

                // The final counts go onto the facet for use in the output templates.
                // Only store the tree if it really is a tree (i.e. there is at least one child).
                if (!empty($facet_cats) && count($facet_cats) > 1) {
                    $facets[$facet_key]['counts'] = $facet_cats;

                    // Add this to the list of facets that have sub-categories.
                    $subcat_facets[] = $facet_key;
                }
            }
        }
    }

    ////////////////////////////////
    // Fetch any articles that match the current selection.
    //

    $articles = array();
    $article_count = 0;
    $all_categories = array();
    $all_categories_cids = array();
    if (!empty($facets)) {
        $article_count = xarModAPIfunc('articles', 'user', 'countitems', $articles_fetch_array);

        $articles_fetch_array['numitems'] = $numitems;
        $articles_fetch_array['startnum'] = $startnum;

        if (!empty($latest)) {
            // Fetch the latest first.
            $articles_fetch_array['sort'] = 'pubdate DESC';
        }

        $articles = xarModAPIfunc('articles', 'user', 'getall', $articles_fetch_array);
        //if (!empty($articles)) foreach($articles as $article) echo "<p><strong>Article: " .$article['title']. "</strong></p>";
        //echo "<pre>Articles:<br />"; var_dump($articles); echo "</pre>";

        // Get the details of all categories in these articles.
        if (!empty($articles)) {
            foreach($articles as $article_key => $article_value) {
                if (!empty($article_value['cids'])) {
                    // Categories are present.
                    foreach($article_value['cids'] as $art_cid) {
                        // Add this to the global list if we have not yet seen it.
                        if (!isset($all_categories_cids[$art_cid])) $all_categories_cids[$art_cid] = $art_cid;
                    }
                }

                // Do transform hooks on the article, while we are looping through them.
                $transform_article = $articles[$article_key]; // Why not $article_value?
                $transform_article['transform'] = array('summary', 'body');
                $transform_article['itemtype'] = $transform_article['pubtypeid'];
                $transform_article['itemid'] = $transform_article['aid'];
                $articles[$article_key] = xarModCallHooks('item', 'transform', $article_value['aid'], $transform_article, 'articles');

                // Also add edit URLs if we have privileges.
                if (!empty($edit_privs[$ptid])) {
                    $articles[$article_key]['editurl'] = xarModURL('articles','admin','modify',
                        array('aid' => $article_value['aid'], 'return_url' => xarServergetCurrentURL(array(), false))
                    );
                }
            }

            if (!empty($all_categories_cids)) {
                $all_categories = xarModAPIfunc(
                    'categories', 'user', 'getcatinfo',
                    array('cids' => $all_categories_cids)
                );
            }

            // Now put the category details onto each article.
            // This makes things easier in the templates.
            foreach($articles as $article_key => $article_value) {
                if (!empty($article_value['cids'])) {
                    $articles[$article_key]['cats'] = array();
                    // Categories are present.
                    foreach($article_value['cids'] as $art_cid) {
                        if (isset($all_categories[$art_cid])) {
                            $articles[$article_key]['cats'][] = $all_categories[$art_cid];
                        }
                    }
                }
            }
        }
    }


    //
    // Create the pager.
    //

    $pager = '';
    if (!empty($articles)) {
        // Include the pager.
        if ($numitems != $default_numitems) {
            $pager = xarTplGetPager($startnum, $article_count,
               xarServerGetCurrentURL(array('startnum' => '%%', 'numitems' => $numitems)),
               $numitems
            );
        } else {
            $pager = xarTplGetPager($startnum, $article_count,
               xarServerGetCurrentURL(array('startnum' => '%%')),
               $numitems
            );
        }

        $pager = trim($pager);
    }

    // Pubtypes will be needed by the templates.
    $pubtypes = xarModAPIFunc('articles', 'user', 'getpubtypes');

    $template_data = compact(
        'facets',
        'filter_facets', 'subcat_facets',
        'article_count', 'articles',
        'pager',
        'q',
        'all_categories', 'pubtypes',
        'aid'
    );

    return array_merge($args, $template_data);
}

?>