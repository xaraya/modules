<?php
/**
 * Navigation location tag.
 *
 *
 */

function navigator_userapi_handle_location_tag( $args )
{
    $locationTag = '';
    $format   = '';
    $array    = '';

    extract($args);

    $errorAddendum = xarML('Please refer to the documentation for this tag for more information.');

    if (!isset($id)) {
        $msg = xarML('Required attribute \'#(1)\' for tag <xar:navigator-location> is missing.', 'id');
        $msg .= $errorAddendum;
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Missing Attributes'), new DefaultUserException($msg));
        return '';
    }

    if (!isset($type)) {
        $msg =  xarML('Required attribute \'#(1)\' for tag <xar:navigator-location> is missing.', 'type');
        $msg .= $errorAddendum;
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Missing Attributes'), new DefaultUserException($msg));
        return '';
    } else {
        if (!eregi('^(crumbtrail|simple)$', $type)) {
            $msg =  xarML('Incorrect \'#(1)\' attribute value [\'#(2)\'] for tag <xar:navigator-location>.', 'type', $type);
            $msg .= xarML('Attribute must be either "crumbtrail" or "simple" - ');
            $msg .= $errorAddendum;
            xarExceptionSet(XAR_USER_EXCEPTION, xarML('Missing Attributes'), new DefaultUserException($msg));
            return '';
        } else {
            // we don't need to pass this to the actual function, so we can
            // get rid of it here.
            unset($args['type']);
        }
    }

    $function = "location_$type";
    $format = 'array(%s)';
    foreach ($args as $key => $value) {
        $items[] = "'$key' => \"$value\"";
    }
    $array = sprintf($format, implode(',', $items));

    $locationTag = sprintf("
        \$tag = xarModFunc('navigator', 'user', '".$function."', %s);
        if (\$tag) {
            echo \$tag;
        }", $array);

    return $locationTag;

}
?>