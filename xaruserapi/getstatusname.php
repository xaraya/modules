<?php
function articles_userapi_getstatusname( $args )
{
    extract($args);
    $states = xarModAPIFunc('articles','user','getstates');
    if (isset($status) && isset($states[$status])) {
        return $states[$status];
    } else {
        return xarML('Unknown');
    }
}
?>
