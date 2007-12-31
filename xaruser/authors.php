<?php

/**
 * Display a single author, or a list of mini-bios.
 *
 * @param mid integer Magazine ID
 * @param mag string Magazine reference
 *
 * @todo Handle pager for multiple authors.
 * @todo Show articles author has written for.
 *
 */

function mag_user_authors($args)
{
    extract($args);
    $return = array();
    
    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,default_numitems_authors,max_author_articles_profile_page'
        )
    ));

    // Fetch the author ID
    xarVarFetch('auid', 'id', $auid, 0, XARVAR_NOT_REQUIRED);

    // Fetch the 'show articles' flag
    xarVarFetch('showarticles', 'bool', $showarticles, false, XARVAR_NOT_REQUIRED);

    // Get the current selected magazine details.
    // All authors must be viewed in the context of a magazine.
    $current_mag = xarModAPIfunc($module, 'user', 'currentmag', $args);
   
    if (!empty($current_mag)) {
        // Extract the current mag details.
        extract($current_mag);

        if (xarSecurityCheck('OverviewMag', 0, 'Mag', "$mid")) {
            $return['mid'] = $mid;
            $return['mag'] = $mag;

            // Get the author details.
            $author_select = array(
                'status' => 'PUBLISHED',
                // TODO: add a pager for the authors; do this later if the numbers get out of hand.
                //'numitems' => $numitems,
                'mid' => $mid,
            );

            if (!empty($auid)) {
                $author_select['auid'] = $auid;

                if (!empty($showarticles)) {
                    // Get articles by this author, if requested.
                    $articles = xarModAPIfunc($module, 'user', 'relatedarticles', 
                        array('mid' => $mid, 'auid' => $auid, 'numitems' => $max_author_articles_profile_page, 'sort' => 'pubdate DESC')
                    );

                    // Group articles by issue
                    if (!empty($articles)) {
                        // Group the articles by issue.
                        $groups_unsorted = array();

                        foreach($articles as $article) {
                            if (!isset($groups_unsorted[$article['issue_id']])) $groups_unsorted[$article['issue_id']] = array();
                            $groups_unsorted[$article['issue_id']][] = $article['aid'];
                        }

                        // Fetch the issues related to these articles.
                        $issue_select = array(
                            'status' => 'PUBLISHED',
                            'mid' => $mid,
                            'iids' => array_keys($groups_unsorted),
                        );

                        $issues = xarModAPIfunc($module, 'user', 'getissues', $issue_select);

                        if (!empty($issues)) {
                            // Order $groups into issue number, descending.
                            $groups = array();
                            foreach($issues as $issue) {
                                if (isset($groups_unsorted[$issue['iid']])) $groups[$issue['iid']] = $groups_unsorted[$issue['iid']];
                            }

                            $return['groups'] = $groups;
                            $return['issues'] = $issues;
                            $return['articles'] = $articles;
                        }
                    }
                }
            }

            $authors = xarModAPIfunc($module, 'user', 'getauthors', $author_select);

            $return['authors'] = $authors;

            // If selecting a single author, then pass that in separately.
            if (count($authors) == 1) {
                $return['author'] = reset($authors);
                $return['auid'] = $return['author']['auid'];
            }
        }
    }

    // Set context information for custom templates and blocks.
    $return['function'] = 'authors';
    xarModAPIfunc($module, 'user', 'cachevalues', $return);

    return $return;
}

?>