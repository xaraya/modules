<?php

/**
 * Return a list of articles for a given magazine issue.
 * No privileges are checked as the assumption is that
 * the privileges are handled at the magazine level.
 * Can be locked down to a specific article.
 */

function mag_listapi_issuearticles($args)
{
    extract($args);

    // TODO/FIXME: this ought to be an option for the display of the drop-down list,
    // not in the supply of the data to the list.
    $max_title_length = 60;

    // Get the issue ID
    xarVarValidate('id', $iid, true);

    // Try fetching from the issue ID cache.
    if (empty($iid) && xarVarIsCached('mag', 'iid')) {
        $iid = xarVarGetCached('mag', 'iid');
    }

    // Try fetching an article ID in the same way.
    xarVarValidate('id', $aid, true);
    if (empty($aid) && xarVarIsCached('mag', 'aid')) {
        $aid = xarVarGetCached('mag', 'aid');
    }

    // TODO: if just an aid is provided, then get the issue from that.

    if (empty($iid)) {
        $return = array(xarML('No magazine issue selected'));
    } else {
        $items = xarModAPIfunc('mag', 'user', 'getarticles', array('iid' => $iid, 'aid' => $aid, 'fieldset' => 'TOC'));

        if (!empty($items)) {
            foreach($items as $item) {
                if (strlen($item['title']) > $max_title_length) {
                    $return[$item['aid']] = substr($item['title'], 0, $max_title_length) . '...';
                } else {
                    $return[$item['aid']] = $item['title'];
                }
            }
        } else {
            $return = array(xarML('No articles found'));
        }
    }

    return $return;
}

?>