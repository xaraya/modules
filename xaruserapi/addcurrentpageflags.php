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
 * @param root_pids array List of vertial root page IDs (optional)
 * @todo Support a 'maxlevels' value to prune anything above a certain level.
 * @todo Support a 'master root' virtual page (ID 0) pointing to the proper root page.
 * @todo Look at the keys again: this function assumes the page keys will always be pids (not true).
 */

function xarpages_userapi_addcurrentpageflags($args)
{
    extract($args);

    if (empty($pagedata) || empty($pid) || !isset($pagedata['pages'][$pid])) {return;}

    if (empty($root_pids) || !is_array($root_pids)) {
        $root_pids = array();
    }

    // Set up a bunch of flags against pages to allow hierarchical menus
    // to be generated. We do not want to make any assumptions here as to
    // how the menus will look and function (i.e. what will be displayed,
    // what will be suppressed, hidden etc) but rather just provide flags
    // that allow a template to build a menu of its choice.
    //
    // The basic flags are:
    // 'depth' - 0 for root, counting up for each subsequent level
    // 'is_ancestor' - flag indicates an ancestor of the current page
    // 'is_child' - flag indicates a child of the current page
    // 'is_sibling' - flag indicates a sibling of the current page
    // 'is_ancestor_sibling' - flag indicates a sibling of an ancestor of the current page
    // 'is_current' - flag indicates the current page
    // 'is_root' - flag indicates the page is a root page of the hierarchy - good
    //      starting point for menus
    // 'has_children' - flag indicates a page has children [done in getpagestree]
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
    $pagedata['ancestors'] = array();
    $this_pid = $pid;

    // TODO: allow a 'virtual root' to stop before we reach the real root page. Used
    // when we are filtering lower sections of a tree. Physically remove pages that
    // do not fall into this range.
    // This *could* happen if a root page is set to INACTIVE and a child page is
    // set as a module alias.
    $ancestor_pids = array();
    while (true) {
        // Set flag for menus.
        $pagedata['pages'][$this_pid]['is_ancestor'] = true;

        // Record the pid, so we don't accidently include this page again.
        array_unshift($ancestor_pids, $this_pid);

        // Reference the page. Note we are working back down the tree
        // towards the root page, so will unshift each page to the front
        // of the ancestors array.
        array_unshift($pagedata['ancestors'], NULL);
        $pagedata['ancestors'][0] =& $pagedata['pages'][$this_pid];

        // Get the parent page.
        $pid_ancestor = $pagedata['pages'][$this_pid]['parent_key'];

        // If there is no parent, then stop.
        // Likewise if this is a page we have already seen (infinite loop protection).
        if ($pid_ancestor == 0 || in_array($pid_ancestor, $ancestor_pids) || in_array($this_pid, $root_pids)) {
            // Make a note of the final root page.
            $root_pid = $this_pid;

            // Since we have reached the 'root' page for the purposes
            // of this ancestry, make sure this root page has no parents
            // by resetting any parent links.
            $pagedata['pages'][$this_pid]['parent_key'] = 0;

            // Reference the root page in the main structure.
            $pagedata['root_page'] =& $pagedata['pages'][$root_pid];

            // Finished the loop.
            break;
        }

        // Move to the parent page and loop.
        $this_pid = $pid_ancestor;
    }

    // Create a 'children' array for children of the current page.
    $pagedata['children'] = array();
    if (!empty($pagedata['current_page']['child_keys'])) {
        foreach ($pagedata['current_page']['child_keys'] as $key => $child) {
            // Set flag for menus. The flag 'is_child' means the page is a
            // child of the 'current' page.
            $pagedata['pages'][$key]['is_child'] = true;
            // Reference the child page from the children array.
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

    // Go through each ancestor and flag up the siblings of those ancestors.
    // They will be all pages that are children of the ancestors, assuming the
    // root ancestor does not have any siblings.
    foreach($pagedata['ancestors'] as $key => $value) {
        if (isset($value['child_keys']) && is_array($value['child_keys'])) {
            //var_dump($value['child_keys']);
            foreach($value['child_keys'] as $value2) {
                $pagedata['pages'][$value2]['is_ancestor_sibling'] = true;
            }
        }
    }

    $pagedata['pid'] = $pid;
    $pagedata['pages'][$pid]['is_current'] = true;

    return $pagedata;
}

?>