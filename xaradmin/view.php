<?php

/**
 * View admin page for magazines.
 * This page provides an overall view of the complete module, with links
 * out to the individual administration pages where relevant.
 *
 * @param 
 *
 */

function mag_admin_view($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module'
        )
    ));

    // Template data.
    $return = array();

    // Get the magazine ID or reference.
    xarVarFetch('mid', 'id', $mid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('mag', 'str:0:30', $mag_ref, '', XARVAR_NOT_REQUIRED);

    // The method we are using to browse, i.e. the view.
    xarVarFetch('view', 'enum:issues:series:articles', $view, 'issues', XARVAR_NOT_REQUIRED);
    $return['view'] = $view;
    
    // Fetch the issue ID.
    xarVarFetch('iid', 'id', $iid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('issue', 'str:0:30', $issue_ref, '', XARVAR_NOT_REQUIRED);

    // Get magazine details.
    if (!empty($mid) || !empty($mag_ref)) {
        // Magazine has been selected.
        $mags = xarModAPIfunc($module, 'user', 'getmags', array('mid' => $mid, 'ref' => $mag_ref));
    } else {
        // No magazine has been selected - get them all.
        $mags = xarModAPIfunc($module, 'user', 'getmags');
    }

    // Check privileges for magazines - remove any that we do not have any privileges on.
    foreach($mags as $mag_key => $mag_value) {
        if (!xarSecurityCheck('EditMag', 0, 'Mag', (string)$mag_value['mid'])) unset($mags[$mag_key]);
    }

    if (!empty($mags)) {
        // We have either one or many magazines.
        if (count($mags) == 1) {
            $mag = reset($mags);
            $mid = $mag['mid'];
            $mag_ref = $mag['ref'];

            $return['mag'] = $mag;
            $return['mid'] = $mid;
            $return['mag_ref'] = $mag_ref;

            // We can now browse by (1) issue, (2) series, or (3) article.
            // We can fetch all issues and series, and then a selection
            // of articles (since th number of articles could be enormous).
            // Eventually issues could become large, but we will deal with
            // that when we get to it.

            // Fetch all series for this magazine.
            $series = xarModAPIfunc($module, 'user', 'getseries', array('mid' => $mid));
            $return['series'] = $series;

            // Fetch all issues (or just a selection, if required).
            if (!empty($iid) || !empty($issue_ref)) {
                $issues = xarModAPIfunc($module, 'user', 'getissues', array('mid' => $mid, 'iid' => $iid, 'ref' => $issue_ref));
            } else {
                $issues = xarModAPIfunc($module, 'user', 'getissues', array('mid' => $mid));
            }

            if (!empty($issues)) {
                if (count($issues) == 1) {
                    // A single issue is selected
                    $issue = reset($issues);
                    $iid = $issue['iid'];
                    $issue_ref = $issue['ref'];

                    $return['issue'] = $issue;
                    $return['iid'] = $iid;
                    $return['issue_ref'] = $issue_ref;

                    // Get the articles for this issue.
                    $articles = xarModAPIfunc($module, 'user', 'getarticles', array('mid' => $mid, 'iid' => $iid, 'fieldset' => 'TOC'));

                    // Get the authors for each article.
                    // Get all the issue authors in one go, grouped by article.
                    // (we want to be nice and efficient here:-)
                    $issue_authors = xarModAPIfunc(
                        $module, 'user', 'getauthors',
                        array('iid' => $iid, 'status_group' => 'DRAFT', 'groupby' => 'article')
                    );

                    foreach($articles as $key => $article) {
                        if (isset($issue_authors[$article['aid']])) {
                            $articles[$key]['authors'] = $issue_authors[$article['aid']];
                        }
                    }

                    $return['articles'] = $articles;
                } else {
                    // Multiple issues - show a list
                    $return['issues'] = $issues;
                }
            }

        } else {
            // Lots of magazines - display a list.
            $return['mags'] = $mags;
        }
    }

    return $return;
}

?>