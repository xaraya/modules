<?php
/**
 * Navigation Menu Element Type: list
 *
 *
 */

function navigator_user_inline_styles( $args )
{

    $styleList = unserialize(xarModGetVar('navigator', 'style.list.inline'));

    if (!isset($styleList) || !is_array($styleList) || !count($styleList)) {
        return '';
    } else {
        $data['styles'] = $styleList;
        return $data;
    }
}

?>