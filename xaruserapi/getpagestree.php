<?php

// Get a tree of pages, with various structures to get navigate it.

function xarpages_userapi_getpagestree($args)
{
    // First get the set of pages.
    // Check out 'getpages' for the complete range of parameters that can be
    // passed in to restrict the pages retrieved.
    $pages = xarMod::apiFunc('xarpages', 'user', 'getpages', $args);

    // Return if no pages found.
    if (empty($pages)) {
        return array('pages' => array(), 'children' => array());
    }

    // Inititalise the return value.
    $tree = array();

    // Create a children list, so the tree can be walked recursively by page key index.
    // Three forms are available, useful in different circumstances:
    // - children_pids: page IDs only
    // - children_pids: linked to the page keys (in the pages array)
    // - children_names: children organised by page name
    // - children_pages: children linked to page records (i.e. the array keys)
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
        // Put links in the pages themselves.
        // Ensure each page has at least an empty array of child keys.
        if (!isset($pages[$key]['child_keys'])) {
            $pages[$key]['child_keys'] = array();
        }
        // Each page has a children array, based on the array keys.
        // If this page has a parent, then add this key to that parent page.
        if ($page['parent_key'] > 0 && isset($pages[$page['parent_key']])) {
            $pages[$page['parent_key']]['child_keys'][$key] = $key;
        }

        // Additional arrays that stand separately to the pages.
        // Add an entry to the children array of pages.
        // Create a new 'parent' page if it does not exist.
        if (!isset($children_keys[$page['parent_key']])) {
            $children_keys[$page['parent_key']] = array();
            $children_names[$page['parent_key']] = array();
            $children_pages[$page['parent_key']] = array();
        }

        // Don't allow item 0 to loop back onto itself.
        // Item 0 points to all the root pages retrieved.
        // FIXME: set 'has_children' for the root page too, if necessary.
        if ($key != 0 || $page['parent_key'] != 0) {
            // Set flag for menus.
            // FIXME: the isset() is necessary because some parent pages
            // are not there. Why not? It shouldn't happen.
            if (isset($pages[$page['parent_key']])) {
                $pages[$page['parent_key']]['has_children'] = true;
            }

            // Create the references.
            $children_keys[$page['parent_key']][$key] = $key;
            $children_names[$page['parent_key']][$page['name']] = $key;
            $children_pages[$page['parent_key']][$key] =& $pages[$key];
        }

        // Calculate the relative nesting level.
        // 'depth' is 0-based. Top level (root node) is zero.
        if (!empty($depthstack)) {
            while (!empty($depthstack) && end($depthstack) < $page['right']) {
                array_pop($depthstack);
                array_pop($pathstack);
            }
        }
        $depthstack[$page['pid']] = $page['right'];
        $pages[$key]['depth'] = (empty($depthstack) ? 0 : count($depthstack) - 1);
        // This item is the path for each page, based on page IDs.
        // It is effectively a list of ancestor IDs for a page.
        // FIXME: some paths seem to get a '0' root ID. They should only have real page IDs.
        $pages[$key]['pidpath'] = array_keys($depthstack);

        $pathstack[$key] = $page['name'];
        // This item is the path for each page, based on names.
        // Imploding it can give a directory-style path, which is handy
        // in admin pages and reports.
        $pages[$key]['namepath'] = $pathstack;
    }

    $tree['pages'] =& $pages;

    $tree['child_refs'] = array(
        'keys' => $children_keys,
        'names' => $children_names,
        'pages' => $children_pages
    );

    return $tree;
}

?>