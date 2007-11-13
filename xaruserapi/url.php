<?php

/**
 * Create a *user* URL.
 * The module and type are generated automatically, with only the
 * func and additional parameters to pass in.
 * Not to be used for admin URLs, as these go to the mag module directly.
 * In essense, this function will take user GUI parameters for a URL, and
 * juggle them around if embedded into xarpages, eventually returning
 * the URL.
 *
 * @params func string The user function; defaults to 'main'.
 * @global xarVarGetCached('mag','pid') integer The xarpages page ID. Optional.
 *
 * @todo Support ability to suppress XML encoding.
 */

function mag_userapi_url($args)
{
    extract($args);

    $type = 'user';

    // A cached variable points to the xarpage if necessary.
    // Only look at the cached value if a pid is not passed in.
    if (!isset($pid) || !is_numeric($pid)) {
        if (xarVarIsCached('mag', 'pid')) {
            $pid = xarVarGetCached('mag', 'pid');
        } else {
            $pid = 0;
        }
    }

    // If a fragment has been requested, then extract it.
    if (isset($args['fragment'])) {
        $fragment = $args['fragment'];
        unset($args['fragment']);
    } else {
        $fragment = NULL;
    }

    if (!empty($pid)) {
        // Magazine is embedded into xarpages, so return a URL to xarpages.
        $args['pid'] = $pid;
        if (isset($args['func'])) {
            // If the func is set (which it should be) then move it to 'mfunc'
            // so it does not clash with the 'display' of xarpages.
            $func = $args['func'];
            $args['mfunc'] = $func;
            unset($args['func']);
        }

        $url = xarModURL('xarpages', $type, 'display', $args, true, $fragment);
    } else {
        // In case the pid is set, but is zero.
        unset($args['pid']);

        if (isset($args['func'])) {
            // If the func is set (which it should be) then remove it
            // so it can be passed in the traditional way.
            $func = $args['func'];
            unset($args['func']);
        } else {
            // Default the function.
            $func = 'main';
        }

        $url = xarModURL('mag', $type, $func, $args, true, $fragment);
    }

    return $url;
}

?>