<?php

/**
 * Return a list of issues for a given magazine.
 * No privileges are checked as the assumption is that
 * the privileges are handled at the magazine level.
 */

function mag_listapi_magissues($args)
{
    extract($args);

    if (!xarVarValidate('id', $mid, true)) $mid = 0;

    // Try fetching from the page cache.
    if (empty($mid) && xarVarIsCached('mag', 'mid')) {
        $mid = xarVarGetCached('mag', 'mid');
    }

    if (empty($mid)) {
        $return = array(xarML('No magazine selected'));
    } else {
        $issues = xarModAPIfunc('mag', 'user', 'getissues', array('mid' => $mid));

        if (!empty($issues)) {
            foreach($issues as $issue) {
                $return[$issue['iid']] = $issue['title'];
            }
        } else {
            $return = array(xarML('No issues found'));
        }
    }

    return $return;
}

?>