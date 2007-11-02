<?php

/**
 * Display the contents of the current issue of the magazine.
 */

function mag_user_current($args)
{
    extract($args);
    $return = array();

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module'
        )
    ));

    // Get the current selected magazine details.
    $current_mag = xarModAPIfunc($module, 'user', 'currentmag', $args);

    if (!empty($current_mag)) {
        extract($current_mag);

        $return['mid'] = $mid;
        $return['mag'] = $mag;

        // Parameters for fetching the latest issue.
        $issue_select = array(
            'mid' => $mid,
            'numitems' => 1,
            'sort' => 'number DESC',
            'status' => 'PUBLISHED',
        );

        // Get the issue.
        $issues = xarModAPIfunc($module, 'user', 'getissues', $issue_select);

        if (count($issues) == 1) {
            $issue = reset($issues);
            $iid = $issue['iid'];

            // Hand control over to the table of contents for this issue.
            return xarModFunc($module, 'user', 'contents', array('mid' => $mid, 'iid' => $iid));
        }
    }
    
    return $return;
}

?>