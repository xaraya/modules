<?php

// Get a single page.

function xarpages_userapi_getpage($args)
{
    // TODO: if the args is a single PID, and the page is cached,
    // then just return that cached page.

    // Get all matching pages. We are hoping we get back just one.
    $pages = xarMod::apiFunc('xarpages', 'user', 'getpages', $args);

    if (empty($pages) || count($pages) > 1) {
        // Too many or not enough pages.
        return;
    } else {
        // Return the only element.
        return reset($pages);
    }
}

?>