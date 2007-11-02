<?php

/**
 * Return a list of styles for a given magazine.
 * The styles are pulled from series in the magazine.
 */

function mag_listapi_magstyles($args)
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
        $series = xarModAPIfunc('mag', 'user', 'getseries', array('mid' => $mid, 'style_isset' => true));

        $return = array('' => xarML('Default style (inherit from series)'));

        if (!empty($series)) {
            foreach($series as $item) {
                $return[$item['style']] = $item['title'] . ' (' . $item['style'] . ')';
            }
        }
    }

    return $return;
}

?>