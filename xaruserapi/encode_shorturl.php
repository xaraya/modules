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
            array('dd_flag' => false, 'key' => 'pid' /*, 'status' => 'ACTIVE'*/)
        );
    }

    // Check that the pid is a valid page.
    if (!isset($pages[$pid])) {
        return;
    }

    $use_shortest_paths = xarModVars::get('xarpages', 'shortestpath');

    // Consume the pid from the get parameters.
    unset($get['pid']);

    // 'Consume' the function now we know we have enough information.
    unset($get['func']);

    // Follow the tree up to the root.
    $pid_follow = $pid;
    while ($pages[$pid_follow]['parent_key'] <> 0) {
        // TODO: could do with an API to get all aliases for a given module in one go.
        if (!empty($use_shortest_paths) && xarModGetAlias($pages[$pid_follow]['name']) == 'xarpages') {
            break;
        }
        array_unshift($path, $pages[$pid_follow]['name']);
        $pid_follow = $pages[$pid_follow]['parent_key'];
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
            if (isset($extra['get']) && is_array($extra['get'])) {
                $get = $extra['get'];
            }
        }
    }

    // Return the path and unconsumed parameters separately.
    // Requires xarMod.php from Xaraya 1.0 RC1 or higher to work.
    return array('path' => $path, 'get' => $get);
}

?>