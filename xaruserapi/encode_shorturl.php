<?php

// Encode the short URL.
// TODO: handle more than just a display function. Site maps could come through here too.

function xarpages_userapi_encode_shorturl($args)
{
    extract($args);

    static $pages = NULL;

    // We need a page ID to continue, for now.
    // TODO: allow this to be expanded to page names.
    if (empty($pid) || ($func != 'main' && $func != 'display')) {
        return;
    }

    // The components of the path.
    $path = array();
    $get = $args;

    // Get the page tree that includes this page.
    // TODO: Do some kind of cacheing on a tree-by-tree basis to prevent
    // fetching this too many times. Every time any tree is fetched, anywhere
    // in this module, it should be added to the cache so it can be used again.
    // For now we are going to fetch all pages, without DD, to cut down on
    // the number of queries, although we are making an assumption that the
    // number of pages is not going to get too high.
    if (empty($pages)) {
        // Fetch all pages, with no DD required.
        $pages = xarModAPIfunc(
            'xarpages', 'user', 'getpages',
            array('dd_flag' => false, 'key' => 'pid', 'status' => 'ACTIVE')
        );
    }

    // Check that the pid is a valid page.
    if (!isset($pages[$pid])) {
        return;
    }

    // Consume the pid from the get parameters.
    unset($get['pid']);

    // Follow the tree up to the root.
    $pid_follow = $pid;
    while ($pages[$pid_follow]['parent'] <> 0) {
        array_unshift($path, $pages[$pid_follow]['name']);
        $pid_follow = $pages[$pid_follow]['parent'];
    }

    // Do the final path part.
    array_unshift($path, $pages[$pid_follow]['name']);

    // If the base path component is not the module alias, then add the
    // module name to the start of the path.
    if (xarModGetAlias($pages[$pid_follow]['name']) != 'xarpages') {
        array_unshift($path, 'xarpages');
    }

    // Now we have the basic path, we can check if there are any custom
    // URL handlers to handle the remainder of the GET parameters.
    // The handler is placed into the xarencodeapi API directory, and will
    // return two arrays: 'path' with path components and 'get' with
    // any unconsumed (or new) get parameters.
    if (!empty($pages[$pid]['encode_url'])) {
        $extra = xarModAPIfunc('xarpages', 'encode', $pages[$pid]['encode_url'], $get, false);

        if (!empty($extra)) {
            // The handler has supplied some further short URL path components.
            if (!empty($extra['path'])) {
                $path = array_merge($path, $extra['path']);
            }
            // Assume it has consumed some GET parameters too.
            // Take what is left (i.e. unconsumed).
            // Note the custom encoder cannot add further GET parameters without
            // supplying further path components. (see below for further notes)
            // NOTE: this is a flaw that should be fixed if this is going to
            // be a core feature: a URL encoder should be able to add further
            // GET parameters or alter existing GET parameters without having
            // to add to the path.
            // TODO: try this for now: so long as the 'get' value is an array,
            // then assume it is the new set of GET parameters. A NULL or non-
            // array can indicate that the current GET parameters should not
            // change. Note the use of is_array(), since the value could be an
            // empty array, indicating that all GET parameters have been consumed.
            if (isset($extra['get']) && is_array($extra['get'])) {
                $get = $extra['get'];
            }
        }
    }


    // Create the URL.
    // TODO: Eventually we should be able to return the array and let
    // xarModURL() do this bit.
    // TODO: this rawurlencode() stuff goes too far by encoding characters
    // it does not really need to encode. The sooner this is centralised,
    // the better.

    // Generate the path parts.
    $url = '';
    foreach ($path as $path_part) {
        $url .= '/' . rawurlencode($path_part);
    }

    // The function name was passed in with the arguments - we don't need that
    // in the get paramaters.
    unset($get['func']);

    // Generate the get parts.
    $sep = '?';
    foreach ($get as $get_name => $get_value) {
        $url .= $sep . rawurlencode($get_name) . '=' . rawurlencode($get_value);
        $sep = '&';
    }

    return $url;
}

?>