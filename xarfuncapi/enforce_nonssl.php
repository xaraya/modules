<?php

/*
 * Ensure the current page is not accessed via SSL. If not, then
 * switch to SSL.
 * This only works for non-shared certificates.
 * @todo Support shared certificates when the Xaraya core supports it.
 */

function xarpages_funcapi_enforce_nonssl($args)
{
    // Get the current URL.
    $url = xarServer::getCurrentURL(array(), false);

    // If we are on an SSL page then redirect.
    if (strpos(strtolower($url), 'https://') === 0) {
        // Switch to non-SSL.
        $url = preg_replace('/^https:/i', 'http:', $url);

        // Set the redirect URL.
        xarResponse::Redirect($url);

        // Tell the caller we want to redirect.
        return false;
    }

    return true;
}

?>