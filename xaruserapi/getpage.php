<?php

// Get a single page.

function xarpages_userapi_getpage($args)
{
    // Get all matching pages. We are hoping we get back just one.
    $pages = xarModAPIfunc('xarpages', 'user', 'getpages', $args);

    if (empty($pages) || count($pages) > 1) {
        // Too many or not enough pages.
        return;
    } else {
        // Return the only element.
        return reset($pages);
    }
}

?>