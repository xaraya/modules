<?php

/*
 * Ensure the current page is accessed via SSL. If not, then
 * switch to SSL.
 * This only works for non-shared certificates.
 * @todo Support shared certificates when the Xaraya core supports it.
 */

function xarpages_funcapi_enforce_ssl($args)
{
    // Get the current URL.
    $url = xarServerGetCurrentURL(array(), false);

    // If we are on a non-SSL page then redirect.
    if (strpos(strtolower($url), 'http://') === 0) {
        // Switch to SSL.
        $url = preg_replace('/^http:/i', 'https:', $url);

        // Set the redirect URL.
        xarResponseRedirect($url);

        // Tell the caller we want to redirect.
        return false;
    }

    return true;
}

?>