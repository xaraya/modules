<?php

/*
 * Add flags and structures to a "pages tree" in respect
 * of the current page within that tree.
 * Takes a "pages tree", as produced by user/getpagestree
 * and adds the necessary flags, returning the tree again.
 * TODO: would probably be more efficient if the pages tree
 * could be passed by reference.
 * @param pagedata array Structure produced by xarpages/user/getpagestree
 * @param pid integer Page ID - the 'current' page.
 * @todo Support a virtual root page, allowing a subtree to act as the main tree.
 * @todo Support a 'maxlevels' value to prune anything above a certain level.
 * @todo Support a 'master root' virtual page (ID 0) pointing to the proper root page.
 */

function xarpages_userapi_addcurrentpageflags($args)
{
    extract($args);

    if (empty($pagedata) || empty($pid) || !isset($pagedata['pages'][$pid])) {return;}

    // Set up a bunch of flags against pages to allow hierarchical menus
    // to be generated. We do not want to make any assumptions here as to
    // how the menus will look and function (i.e. what will be displayed,
    // what will be suppressed, hidden etc) but rather just provide flags
    // that allow a template to build a menu of its choice.
    //
    // The basic flags are:
    // 'depth' - 0 for root, counting up for each subsequent level [done]
    // 'is_ancestor' - flag indicates an ancestor of the current page [done]
    // 'is_child' - flag indicates a child of the current page [done]
    // 'is_sibling' - flag indicates a sibling of the current page [done]
    // 'is_current' - flag indicates the current page [done]
    // 'is_root' - flag indicates the page is a root page of the hierarchy - good
    //      starting point for menus [done]
    // 'has_children' - flag indicates a page has children [done - in getpagestree]
    //
    // Any page will have a depth flag, and may have one or more of the
    // remaining flags.
    // NOTE: with the exception of the following, all the above flags are
    // set in previous loops.

    // Point the current page at the page in the tree.
    $pagedata['current_page'] =& $pagedata['pages'][$pid];

    // Create an ancestors array.
    // Shift the pages onto the start of the array, so the resultant array
    // is in order furthest ancestor towards the current page.
    // The ancestors array includes the current page.
    // TODO: stop at a non-ACTIVE page. Non-ACTIVE pages act as blockers
    // in the hierarchy.
    // Ancestors will include self - filter out in the template if required.
    $ancestors = array();
    $pid_ancestor = $pid;

    // TODO: protect against infinite loops if we never reach a parent of zero.
    // TODO: allow a 'virtual root' to stop before we reach the real root page. Used
    // when we are filtering lower sections of a tree. Physically remove pages that
    // do not fall into this range.
    // This *could* happen if a root page is set to INACTIVE and a child page is
    // set as a module alias.
    while ($pagedata['pages'][$pid_ancestor]['parent_pid'] > 0) {
        // Set flag for menus.
        $pagedata['pages'][$pid_ancestor]['is_ancestor'] = true;
        // Reference the page. Note we are working back down the tree
        // towards the root page, so will unshift each page to the front
        // of the ancestors array.
        array_unshift($ancestors, &$pagedata['pages'][$pid_ancestor]);
        $pid_ancestor = $pagedata['pages'][$pid_ancestor]['parent_key'];
    }
    $pagedata['pages'][$pid_ancestor]['is_ancestor'] = true;
    array_unshift($ancestors, &$pagedata['pages'][$pid_ancestor]);
    $pagedata['ancestors'] = $ancestors;

    // Create a 'children' array for children of the current page.
    $pagedata['children'] = array();
    if (!empty($pagedata['current_page']['child_keys'])) {
        foreach ($pagedata['current_page']['child_keys'] as $key => $child) {
            // Set flag for menus.
            $pagedata['pages'][$key]['is_child'] = true;
            // Reference the child page.
            $pagedata['children'][$key] =& $pagedata['pages'][$child];
        }
    }

    // TODO: create a 'siblings' array.
    // Siblings are the children of the current page parent.
    // The root page will have no siblings, as we want to keep this in
    // a single tree.
    // Siblings will include self - filter out in the template if necessary.
    $pagedata['siblings'] = array();
    if (!empty($pagedata['current_page']['parent_key'])) {
        // Loop though all children of the parent.
        foreach ($pagedata['pages'][$pagedata['current_page']['parent_key']]['child_keys'] as $key => $child) {
            // Set flag for menus.
            $pagedata['pages'][$key]['is_sibling'] = true;
            // Reference the page.
            $pagedata['siblings'][$key] =& $pagedata['pages'][$child];
        }
    }

    $pagedata['pid'] = $pid;
    $pagedata['pages'][$pid]['is_current'] = true;

    return $pagedata;
}

?>