<?php
/**
 * Navigation menu tag.
 *
 *
 */

function navigator_userapi_handle_menu_tag( $args )
{
    $menuTag = '';
    $format   = '';
    $array    = '';

    extract($args);

    if (!isset($emptygroups)) {
        $args['emptygroups'] = 'hide';
    } else {
        $args['emptygroups'] = (strtolower($emptygroups) == 'hide') ? 'hide' : 'show';
    }

    if (!isset($exclude)) {
        $args['excludecids'] = '';
    } else {
        $args['excludecids'] = $exclude;
    }

    if (!isset($rename)) {
        $args['rename'] = '';
    }

    if (!isset($intersects)) {
        $args['intersects'] = '';
    }

    $errorAddendum = xarML('See tag documentation.');

    if (!isset($id)) {
        $msg = xarML('Required attribute \'#(1)\' for tag <xar:navigator-menu> is missing.', 'id');
        $msg .= $errorAddendum;
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Missing Attribute'), new DefaultUserException($msg));
        return '';
    }

    if (!isset($base) || !eregi('^(primary|secondary)$', $base)) {
        $msg = xarML('Required attribute \'#(1)\' for tag <xar:navigator-menu> is missing.', 'base');
        $msg .= $errorAddendum;
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Missing Attribute'), new DefaultUserException($msg));
        return '';
    } elseif (!eregi('^(primary|secondary)$', $base)) {
        $msg =  xarML('Incorrect \'#(1)\' attribute value [\'#(2)\'] for tag <xar:navigation-menu>.', 'base', $base);
        $msg .= xarML('Attribute value must be either "primary" or "secondary" - ');
        $msg .= $errorAddendum;
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Invalid Attribute'), new DefaultUserException($msg));
        return '';
    }

    if (!isset($type)) {
        $msg =  xarML('Required attribute \'#(1)\' for tag <xar:navigator-menu> is missing.', 'type');
        $msg .= $errorAddendum;
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Missing Attribute'), new DefaultUserException($msg));
        return '';
    } else {
        if (!eregi('^(list|images|branch)$', $type)) {
            $msg =  xarML('Incorrect \'#(1)\' attribute value [\'#(2)\'] for tag <xar:navigation-menu>.', 'type', $type);
            $msg .= xarML('Attribute must be either "list" or "images" - ');
            $msg .= $errorAddendum;
            xarExceptionSet(XAR_USER_EXCEPTION, xarML('Invalid Attribute'), new DefaultUserException($msg));
            return '';
        } else {
            // we don't need to pass this to the actual function, so we can
            // get rid of it here.
            unset($args['type']);
        }
    }

    $function = "menutype_$type";
    $format = 'array(%s)';

    $array = var_export($args, TRUE);

    $menuTag = sprintf("
        \$tag = xarModFunc('navigator', 'user', '".$function."', %s);
        if (\$tag) {
            echo \$tag;
        }", $array);

    return $menuTag;

}
?>
