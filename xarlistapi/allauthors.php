<?php

/**
 * Return a list of all defined authors.
 */

function mag_listapi_allauthors($args)
{
    extract($args);

    $items = xarModAPIfunc('mag', 'user', 'getauthors', array('sort' => 'name asc'));

    if (!empty($items)) {
        foreach($items as $item) {
            $return[$item['auid']] = $item['name'] . ' (' . $item['auid'] . ')';
        }
    } else {
        $return[0] = xarML('-- No authors defined --');
    }

    return $return;
}

?>