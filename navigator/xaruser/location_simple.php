<?php
/**
 * Navigation Menu Element Type: list
 *
 *
 */

function navigator_user_location_simple( $args )
{
    extract($args);

    // pass through to the template all extra keys
    foreach ($args as $key => $value) {
        $data[$key] = $value;
    }

    $current_cids = xarModAPIFunc('navigator', 'user', 'get_current_cats');

    if (empty($current_cids)) {
        return;
    } else {
        extract($current_cids);
    }

    if (xarModGetVar('navigator', 'style.matrix')) {
        $matrix = TRUE;
    } else {
        $matrix = FALSE;
    }

    if ($matrix) {
        $secDef = xarModGetVar('navigator', 'categories.secondary.default');

        if ($secondary['id'] == $secDef) {
            $data['header'] = $primary['name'];
        } else {
            $data['header'] = $secondary['name'];
        }
    } else {
        $data['header'] = $primary['name'];
    }

    return $data;
}

?>