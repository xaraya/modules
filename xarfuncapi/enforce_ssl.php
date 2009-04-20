<?php

/*
 * Ensure the current page is accessed via SSL. If not, then
 * switch to SSL.
 * This only works for non-shared certificates.
 * @todo Support shared certificates when the Xaraya core supports it.
 * @todo Revisit the check to Site.Core.EnableSecureServer if the core https handling gets changed
 */

function xarpages_funcapi_enforce_ssl($args)
{
    // Only do this if 'Allow SSL' option is set, because if not set,
    // then xarServer::getCurrentURL() tends to lie (it returns 'http'
    // even if the current page is 'https').
    if (xarConfigVars::get(null,'Site.Core.EnableSecureServer') != true) {
        // Bail out if secure server is not enabled.
        return true;
    }

    // Get the current URL.
    $url = xarServer::getCurrentURL(array(), false);

    // If we are on a non-SSL page then redirect.
    // Note: this only works with non-shared certificates, where the
    // SSL and non-SSL addresses are the same. To handle it any other way
    // will involve changes to the core. Hopefully that will be done one
    // day.
    if (strpos(strtolower($url), 'http://') === 0) {
        // Switch to SSL.
        $url = preg_replace('/^http:/i', 'https:', $url);

        // Set the redirect URL.
        xarResponse::Redirect($url);

        // Tell the caller we want to redirect.
        return false;
    }

    return true;
}

?>