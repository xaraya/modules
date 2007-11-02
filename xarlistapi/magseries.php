<?php

/**
 * Return a list of series for a given magazine.
 * No privileges are checked as the assumption is that
 * the privileges are handled at the magazine level.
 */

function mag_listapi_magseries($args)
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
        $items = xarModAPIfunc('mag', 'user', 'getseries', array('mid' => $mid));

        $return = array(xarML('Not connected to a series'));

        if (!empty($items)) {
            foreach($items as $item) {
                $return[$item['sid']] = $item['title'];
            }
        }
    }

    return $return;
}

?>