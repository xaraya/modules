<?php

/**
 * Display the table of contents for a single magazine issue
 *
 * @param mid integer Magazine ID; or
 * @param mag string Magazine reference
 * @param iid integer Issue ID; or
 * @param issue string Issue reference
 *
 */

function mag_user_contents($args)
{
    extract($args);
    $return = array();

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,sort_default_articles_toc,image_article_main_vpath'
        )
    ));

    // Issue ID or reference
    xarVarFetch('iid', 'id', $iid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('issue', 'str:1:30', $issue_ref, '', XARVAR_NOT_REQUIRED);

    // Issue ID will override reference.
    if (!empty($issue_ref) && !empty($iid)) $issue_ref = '';

    // Get the current selected magazine details.
    // TODO: if in admin preview mode, open up to inactive magazines, with unlimited 'showin' properties.
    $current_mag = xarModAPIfunc($module, 'user', 'currentmag', $args);
   
    if (!empty($current_mag)) {
        // Extract the current mag details.
        extract($current_mag);
        $return['mid'] = $mid;
        $return['mag'] = $mag;

        // Get the issue details.
        $issue_select = array();

        if (!empty($iid)) $issue_select['iid'] = $iid;
        if (!empty($issue_ref)) $issue_select['ref'] = $issue_ref;
        $issue_select['numitems'] = 2;
        $issue_select['mid'] = $mid;

        // TODO: in iadmin preview mode, any status will do.
        $issue_select['status'] = array('PUBLISHED');

        // Get the issue.
        $issues = xarModAPIfunc($module, 'user', 'getissues', $issue_select);

        // We must have exactly one issue.
        if (count($issues) == 1) {
            $issue = reset($issues);
            $iid = $issue['iid'];
            $return['iid'] = $iid;
            $return['issue'] = $issue;

            // Get the articles and series for this magazine, organised as a table of contents.
            // TODO: in admin preview mode, suppress status and showin properties ('status_group')
            $toc = xarModAPIfunc($module, 'user', 'gettoc', array('mag' => $mag, 'issue' => $issue));
            if (!empty($toc)) $return = array_merge($return, $toc);
        }
    }

    return $return;
}

?>