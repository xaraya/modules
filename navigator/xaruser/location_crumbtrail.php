<?php
/**
 * Navigation Menu Element Type: list
 *
 *
 */

function navigator_user_location_crumbtrail( $args )
{
    extract($args);
    // Get the catid from input:
    $matrix   = xarModGetVar ('navigator', 'style.matrix');
    $catList  = xarModAPIFunc('navigator', 'user', 'get_current_cats');
    $secDef   = xarModGetVar ('navigator', 'categories.secondary.default');

    if (!isset($matrix) || empty($matrix)) {
        $data['matrix'] = 0;
    } else {
        $data['matrix'] = 1;
    }

    if ($matrix && (count($catList) <= 1)) {
        return '';
    }

    if (!count($catList) || empty($catList)) {
        return;
    }

    // set up the first one
    $trail = $catList['primary']['name'] . ' &gt; ' . $catList['secondary']['name'];

    foreach ($args as $key => $value) {
        $data[$key] = $value;
    }

    $data['trail']['text'] = $trail;
    $data['trail']['raw']  = $catList;
    $data['secondary_default'] = $secDef;

    return $data;
}

?>