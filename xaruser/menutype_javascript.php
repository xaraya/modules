<?php
/**
 * Navigation Menu Element Type: javascript
 *
 *
 */

function navigator_user_menutype_javascript( $args )
{
    if (!xarSecurityCheck('ViewNavigatorMenu', 0, 'Menu', $args['id'], 'navigator')) {
        return;
    }

    extract($args);

    return $data;
}

?>