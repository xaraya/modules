<?php
/**
 * navigator image tag.
 *
 *
 */

function navigator_userapi_handle_image_tag( $args )
{
    $imageTag = '';
    $format   = '';
    $array    = '';

    extract($args);

    if (!isset($id))) {
        $msg = xarML('Required attribute \'#(1)\' for tag <xar:navigator-image> is missing. See tag documentation.', 'id');
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Missing Attributes'), new DefaultUserException($msg));
        return '';
    }

    $format = 'array(%s)';
    foreach ($args as $key => $value) {
        $items[] = "'$key' => \"$value\"";
    }
    $array = sprintf($format, implode(',', $items));

    $imageTag = sprintf("
        \$tag = xarModFunc('navigator', 'user', 'dynamic_image', %s);
        if (\$tag) {
            echo \$tag;
        }", $array);

    return $imageTag;

}
?>