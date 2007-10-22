<?php

/**
 * Overview of available series for a given magazine.
 *
 * @param mid integer Magazine ID
 * @param mag string Magazine reference
 *
 * Things to display:
 * - all available series for a magazine (abstracts)
 * - details for a single series
 * - list of issues the series appears in
 * - list of articles in the series
 *   (Last one could probably just be links to the search page)
 *
 */

function mag_user_series($args)
{
    extract($args);

    extract($args);
    $return = array();

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module'
        )
    ));

    // Series ID or reference
    xarVarFetch('sid', 'id', $sid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('series', 'str:1:30', $series_ref, '', XARVAR_NOT_REQUIRED);

    // Flag to request the showing of articles and/or issues.
    xarVarFetch('show', 'enum:articles:issues', $show, '', XARVAR_NOT_REQUIRED);

    // Series ID will override reference.
    if (!empty($series_ref) && !empty($sid)) $series_ref = '';

    // Get the current selected magazine details.
    $current_mag = xarModAPIfunc($module, 'user', 'currentmag', $args);
   
    if (!empty($current_mag)) {
        // Extract the current mag details.
        extract($current_mag);
        $return['mid'] = $mid;
        $return['mag'] = $mag;

        // Get the series details.
        $series_select = array();

        // Single series selected.
        if (!empty($sid)) $series_select['sid'] = $sid;
        if (!empty($series_ref)) $series_select['ref'] = $series_ref;

        $series_select['status'] = 'ACTIVE';
        $series_select['sort'] = 'display_order ASC';
        $series_select['mid'] = $mid;

        // Get the series (single or multiple).
        $series = xarModAPIfunc($module, 'user', 'getseries', $series_select);

        // If we have just one series, and the user has requsted additional details,
        // then fetch those details now.
        if (count($series) == 1) {
            $one_series = reset($series);

            if ($show == 'articles') {
                // Fetch articles for this series.
                // TODO: support the pager.
                $article_select = array(
                    'mid' => $mid,
                    'sid' => $one_series['sid'],
                    'status' => 'PUBLISHED',
                    'fieldset' => 'TOC',
                );

                $articles = xarModAPIfunc($module, 'user', 'getarticles', $article_select);

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

        $return['show'] = $show;
        $return['series'] = $series;
    }


    return $return;
}

?>