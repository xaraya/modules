<?php

// Get a tree of pages, with various structures to get navigate it.

function xarpages_userapi_getpagestree($args)
{
    // First get the set of pages.
    // Check out 'getpages' for the complete range of parameters that can be
    // passed in to restrict the pages retrieved.
    $pages = xarModAPIfunc('xarpages', 'user', 'getpages', $args);

    // Return if no pages found.
    if (empty($pages)) {
        return;
    }

    // Inititalise the return value.
    $tree = array();

    // Create a children list, so the tree can be walked recursively by page key index.
    // Three forms are available, useful in different circumstances:
    // - children_pids: page IDs only
    // - children_names: children organised by page name
    // - children: children linked to page records (i.e. the array keys)
    // Note the pages version contains linked references to each page, to save memory
    // and allow changes made to the main 'pages' array to be visible in the 'pages'
    // children array.
    $children_pids = array();
    $children_keys = array();
    $children_names = array();
    $children_pages = array();
    $depthstack = array();
    $pathstack = array();

    // Create some additional arrays to help navigate the [flat] pages array.
    foreach($pages as $key => $page) {
        // Each page has a children array, based on the array keys.
        if ($page['parent'] > 0) {
            $pages[$page['parent']]['children'][$key] = $key;
        }

        // Add an entry to the children array of pages.
        // Create a new 'parent' page if it does not exist.
        if (!isset($children_pids[$page['parent']])) {
            $children_pids[$page['parent_pid']] = array();
            $children_keys[$page['parent']] = array();
            $children_names[$page['parent']] = array();
            $children_pages[$page['parent']] = array();
        }
        // Don't allow item 0 to loop back onto itself.
        // Item 0 points to all the root pages retrieved.
        if ($key != 0 || $page['parent'] != 0) {
            $children_pids[$page['parent_pid']][$page['pid']] = $page['pid'];
            $children_keys[$page['parent']][$key] = $key;
            $children_names[$page['parent']][$page['name']] = $key;
            $children_pages[$page['parent']][$key] =& $pages[$key];
        }

        // Calculate the relative nesting level. Top level (root node) is zero.
        if (!empty($depthstack)) {
            while (!empty($depthstack) && end($depthstack) < $page['right']) {
                array_pop($depthstack);
                array_pop($pathstack);
            }
        }

        // 'depth' is 0-based
        $depthstack[$page['pid']] = $page['right'];
        $pages[$key]['depth'] = (empty($depthstack) ? 0 : count($depthstack) - 1);
        // This item is the path for each page, based on IDs.
        // FIXME: some paths seem to get a '0' root ID. They should only have real page IDs.
        $pages[$key]['pidpath'] = array_keys($depthstack);

        $pathstack[] = $page['name'];
        // This item is the path for each page, based on names.
        // Imploding it can give a directory-type of path.
        $pages[$key]['namepath'] = $pathstack;
    }

    $tree['pages'] =& $pages;

    $tree['children'] = array();
    $tree['children']['pids'] = $children_pids;
    $tree['children']['keys'] = $children_keys;
    $tree['children']['names'] = $children_names;
    $tree['children']['pages'] = $children_pages;

    return $tree;
}

?>