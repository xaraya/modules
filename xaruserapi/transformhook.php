<?php

function xarpages_userapi_transformhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        return;
    }

    // Do we transform internal reference reference links?
    // e.g. <a href="#foo"> => <a href="http://this.domain/path/page#foo">
    // TODO: this is just HTML for now. How do we apply these transform
    // hooks in a more generic way in the future? I would guess they should
    // apply more to the complete page, but not sure much beyond that.
    $transformref = xarModVars::get('xarpages', 'transformref');
    if (empty($transformref)) {
        $transformref = 0;
    }

    if (is_array($extrainfo)) {
        // If the 'transform' array is set, then it lists the elements that
        // should be transformed.
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            $keys = array_flip($extrainfo['transform']);
        } else {
            $keys = array();
        }

        // Loop through elements to transform.
        foreach($extrainfo as $key => $text) {
            if (empty($keys) || isset($keys[$key])) {
                if ($transformref) {
                    $extrainfo[$key] = preg_replace('/(<a[^>]+href=")#/i', '$1'.$_SERVER['REQUEST_URI'].'#', $extrainfo[$key]);
                }
            }
        }
    } else {
        if ($transformref) {
            $extrainfo = preg_replace('/(<a[^>]+href=")#/i', '$1'.$_SERVER['REQUEST_URI'].'#', $extrainfo);
        }
    }

    return $extrainfo;
}

?>