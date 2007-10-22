<?php

/**
 * Display a single article.
 *
 * @param mid integer Magazine ID
 * @param mag string Magazine reference
 *
 * @todo Fetch lots of other useful information and cache it, such as other articles, related issues etc.
 *
 */

function mag_user_article($args)
{
    extract($args);
    $return = array();
    
    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,default_numitems_mags,max_numitems_mags,image_article_main_vpath,premium_policy_bypass_ip'
        )
    ));

    // Fetch the article ID or reference.
    xarVarFetch('aid', 'id', $aid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('article', 'str:0:30', $article_ref, '', XARVAR_NOT_REQUIRED);

    // Optional issue details
    xarVarFetch('iid', 'id', $iid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('issue', 'str:0:30', $issue_ref, '', XARVAR_NOT_REQUIRED);

    // aid overrides article
    if (!empty($article_ref) && !empty($aid)) $article_ref = '';

    // Get the current selected magazine details.
    $current_mag = xarModAPIfunc($module, 'user', 'currentmag', $args);
   
    if (!empty($current_mag)) {
        // Extract the current mag details.
        extract($current_mag);
        $return['mid'] = $mid;
        $return['mag'] = $mag;

        // Get the article details.
        $article_select = array(
            'status' => 'PUBLISHED',
            'numitems' => 2,
            'mid' => $mid,
        );

        if (isset($aid)) $article_select['aid'] = $aid;
        if (isset($article_ref)) $article_select['ref'] = $article_ref;

        // If the issue ID has been supplied, then use it, in case the article_ref is not unique.
        if (empty($iid) && !empty($issue_ref)) {
            // Get the issue ID by using the supplied reference.
            $issues = xarModAPIfunc(
                $module, 'user', 'getissues',
                array(
                    'status' => 'PUBLISHED',
                    'numitems' => 1,
                    'mid' => $mid,
                    'ref' => $issue_ref,
                )
            );
            if (!empty($issues)) {
                $issue = reset($issues);
                $iid = $issue['iid'];
            }
        }
        if (isset($iid)) $article_select['iid'] = $iid;

        $articles = xarModAPIfunc($module, 'user', 'getarticles', $article_select);

        if (count($articles) == 1) {
            $article = reset($articles);

            // Get the issue details (if not already fetched)
            // If an attempt has been made to fetch the issue already, then don't try again,
            // even if that attempt did not result in any issues selected.
            if (!isset($issues)) {
                $issues = xarModAPIfunc(
                    $module, 'user', 'getissues',
                    array(
                        'status' => 'PUBLISHED',
                        'numitems' => 1,
                        'aid' => $article['issue_id'],
                    )
                );
            }

            // If there is no issue, or the issue is not published, then we cannot
            // display this article. Check we have exactly one article.
            if (count($issues) == 1) {
                $issue = reset($issues);

                // Get the main image path, transformed.
                if (isset($article['image1'])) {
                    $article['image1_path'] = xarModAPIfunc(
                        'mag', 'user', 'imagepaths',
                        array(
                            'path' => $image_article_main_vpath,
                            'fields' => array(
                                'mag_ref' => $mag['ref'],
                                'issue_ref' => $issue['ref'],
                                'article_ref' => $article['ref'],
                                'article_id' => $article['aid'],
                                'image1' => $article['image1'],
                            )
                        )
                    );
                }

                // The premium flag should fall back to the issue and then the magazine
                // if not set on the article.
                // TODO: this is the same as in the 'gettoc' APi - could be shared?
                if (empty($article['premium'])) {
                    // The premium flag is not set on the article.
                    if (!empty($issue['premium'])) {
                        // Fall back to the issue flag.
                        $articles[$key]['premium'] = $issue['premium'];
                    } elseif (!empty($mag['premium'])) {
                        // Fall back to the magazine flag.
                        $articles[$key]['premium'] = $mag['premium'];
                    } else {
                        // Default to 'OPEN'.
                        $articles[$key]['premium'] = 'OPEN';
                    }
                }
                
                // Get the [optional] series details.
                if (!empty($article['series_id'])) {
                    $series = xarModAPIfunc(
                        $module, 'user', 'getseries',
                        array(
                            'status' => 'ACTIVE',
                            'mid' => $mid,
                            'sid' => $article['series_id'],
                        )
                    );

                    if (count($series) == 1) {
                        $series = reset($series);
                        $return['series'] = $series;
                    }
                }

                // Get all authors for this article.
                $authors = xarModAPIfunc(
                    $module, 'user', 'getauthors',
                    array('mid' => $mid, 'iid' => $issue['iid'], 'aid' => $article['aid'])
                );

                // Get the table of contents, for use as a navigation tool.
                // TODO: This information would be very useful to the navigation blocks, so cache it.
                $toc = xarModAPIfunc($module, 'user', 'gettoc', array('mag' => $mag, 'issue' => $issue));

                // Do a bit of organisation in the TOC.
                // First find out where we are in the linear list of articles.
                // The position will be zero-indexed.
                $article_ids = array_keys($toc['articles']);
                $article_position = array_search($article['aid'], $article_ids);
                $toc['article_ids'] = $article_ids;
                $toc['article_position'] = $article_position;

                // Get the IDs of previous and next articles.
                $toc['prev_aid'] = (isset($article_ids[$article_position-1]) ? $article_ids[$article_position-1] : 0);
                $toc['next_aid'] = (isset($article_ids[$article_position+1]) ? $article_ids[$article_position+1] : 0);

                // Send the toc to the template, with all its extra bits.
                $return['toc'] = $toc;

                $return['issue'] = $issue;
                $return['article'] = $article;
                $return['authors'] = $authors;
            }
        }
    }

    // Determine the template style to use.
    // The series will give us the fallback style, with the article being
    // allowed to override it.
    // We will also check here that appropriate templates actually exist before
    // trying to call them up.
    if (!empty($article)) {
        // Default is empty, i.e. default style.
        $style = '';

        if (!empty($article['style'])) {
            $style = $article['style'];
        } elseif (!empty($series) && !empty($series['style'])) {
            $style = $series['style'];
        } else {
            $style = 'default';
        }

        // We will try the user-article[-mag_ref] template first, and from there
        // call up sub-templates using the style attribute, e.g. article-body[-style].
        $return['style'] = $style;
    }

    // Call up an article template specific to the magazine, if available.
    // Templates tried will be user-article[-mag_reference] in the current theme first,
    // falling back to the module.
    if (!empty($mag['ref']) && !empty($style)) {
        // Equivalent to the following, but still allows us to return an array:
        // return xarTplModule($module, 'user', 'article', $return, $mag['ref']);
        // Returning an array is important as it allows us to use this GUI
        // function as an API.
        $return['_bl_template'] = $mag['ref'];
    }

    // Flag where the request has come from: the localhost or not.
    // This allows certain requests to bypass any premium-related
    // restrictions, e.g. a local spidering search engine.
    // It does not bypass security restrictions though, only the
    // premium policy.

    if (!empty($premium_policy_bypass_ip) && !empty($GLOBALS['HTTP_SERVER_VARS']['SERVER_ADDR']) && !empty($GLOBALS['HTTP_SERVER_VARS']['REMOTE_ADDR'])) {
        $ips = explode(',', str_replace('localhost', $GLOBALS['HTTP_SERVER_VARS']['SERVER_ADDR'], $premium_policy_bypass_ip));
        if (in_array($GLOBALS['HTTP_SERVER_VARS']['REMOTE_ADDR'], $ips)) {
            $premium_bypass = true;
        } else {
            $premium_bypass = false;
        }
    } else {
        $premium_bypass = false;
    }

    // Pass the boolean into the templates for processing.
    $return['premium_bypass'] = $premium_bypass;

    return $return;
}

?>