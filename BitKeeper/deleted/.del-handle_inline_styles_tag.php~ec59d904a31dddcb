<?php
/**
 * Navigation location tag.
 *
 *
 */

function navigator_userapi_handle_inline_styles_tag( $args )
{
    static $loop = FALSE;

    if (!$loop) {
        $loop = TRUE;
    } else {
        return '';
    }

    $locationTag = '';
    $format   = '';
    $array    = '';

    extract($args);

    $errorAddendum = xarML('Please refer to the documentation for ' .
                           'this tag for more information.');

    $format = 'array(%s)';
    foreach ($args as $key => $value) {
        $items[] = "'$key' => \"$value\"";
    }

    if (!isset($items)) {
        $items = array();
    }

    $array = sprintf($format, implode(',', $items));

    $Tag = sprintf("
        \$tag = xarModFunc('navigator', 'user', 'inline_styles', %s);
        if (\$tag) {
            echo \$tag;
        }", $array);

    return $Tag;

}
?>
