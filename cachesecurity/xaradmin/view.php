<?php

/**
 * show the links for module items
 */
function cachesecurity_admin_view($args)
{ 
    if (!xarSecurityCheck('AdminCacheSecurity')) return;

    $data = array();

    return $data;
}

?>
