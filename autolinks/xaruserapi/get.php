<?php

/**
 * get a specific link
 * @param $args['lid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function autolinks_userapi_get($args)
{
    $links = xarModAPIfunc('autolinks', 'user', 'getall', $args);

    if (empty($links)) {return $links;}

    if (count($links) > 1) {
        // Too many matches.
        $msg = xarML('Too many links match criteria');
        xarExceptionSet(
            XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }

    // Just return the first (and only) link.
    return reset($links);
}

?>