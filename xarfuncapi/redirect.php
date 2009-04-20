<?php

/*
 * Redirect to a URL.
 * @param url string URL to redirect to
 * Note: ensure the URL is not XML-encoded
 */

function xarpages_funcapi_redirect($args)
{
    // The DD field 'url' must be created to hold the URL.
    if (isset($args['current_page']['dd']['url']) && !empty($args['current_page']['dd']['url'])) {
        xarResponse::Redirect($args['current_page']['dd']['url']);

        // Return false to tell the caller to stop further processing.
        return false;
    }

    return;
}

?>