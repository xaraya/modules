<?php

function xarpages_user_display($args)
{
    extract($args);

    // Fetch the page ID.
    // This may have been calculated from a path in the
    // short URL decode function.
    xarVarFetch('pid', 'id', $pid, 0, XARVAR_NOT_REQUIRED);

    // TODO: much of this information may have been retrieved during short URL
    // decoding, so need not be done again.

    // If no PID supplied, get the default PID.
    if (empty($pid)) {
        $pid = xarModGetVar('xarpages', 'defaultpage');
    }

    // Get the current page details.
    // Only fetch active pages.
    // TODO: allow the administrator to display other statuses.
    if (!empty($pid)) {
        $current_page = xarModAPIfunc(
            'xarpages', 'user', 'getpage',
            array('pid' => $pid, 'status' => 'ACTIVE')
        );

        // If no page found, try the 'notfound' page.
        // TODO: If this page exists, we need to pass some additional
        // information to it, so it can render a sensible message or
        // make some choices.
        if (empty($current_page)) {
            $pid = xarModGetVar('xarpages', 'notfoundpage');
        }

        if (!empty($pid)) {
            $current_page = xarModAPIfunc(
                'xarpages', 'user', 'getpage',
                array('pid' => $pid, 'status' => 'ACTIVE')
            );
        }
    }

    if (empty($current_page)) {
        // Get the PID for the 'error' page.
        $pid = xarModGetVar('xarpages', 'errorpage');

        if (!empty($pid)) {
            $current_page = xarModAPIfunc(
                'xarpages', 'user', 'getpage',
                array('pid' => $pid, 'status' => 'ACTIVE')
            );
        } else {
            // Give up: we could not find the requested page, the notfound page nor the error page.
            $msg = xarML('Could not load page.');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }
    }

    // TODO: allow relevent privileges to overlook the status.
    // Get the complete tree for this section of pages.
    $data = xarModAPIfunc(
        'xarpages', 'user', 'getpagestree',
        array(
            'tree_contains_pid' => $pid,
            'dd_flag' => true,
            'key' => 'pid',
            'status' => 'ACTIVE'
        )
    );

    // Point the current page at the page in the tree.
    $data['current_page'] =& $data['pages'][$pid];

    // Create an ancestors array.
    // We don't do that in getpagestree() since that function does not
    // have any knowledge of the 'current' page.
    // Shift the pages onto the start of the array, so the resultant array
    // is in order furthest ancestor towards the current page.
    // The ancestors array includes the current page.
    // TODO: stop at an non-ACTIVE page. Non-ACTIVE pages act as blockers
    // in the hierarchy.
    // Ancestors will include self - filter out in the template if required.
    $ancestors = array();
    $pid_ancestor = $pid;
    // TODO: protect against infinite loops if we never reach a parent of zero.
    // This *could* happen if a root page is set to INACTIVE and a child page is
    // set as a module alias.
    while ($data['pages'][$pid_ancestor]['parent_pid'] > 0) {
        array_unshift($ancestors, &$data['pages'][$pid_ancestor]);
        $pid_ancestor = $data['pages'][$pid_ancestor]['parent_key'];
    }
    array_unshift($ancestors, &$data['pages'][$pid_ancestor]);
    $data['ancestors'] = $ancestors;

    // Create a 'children' array for children of the current page.
    $data['children'] = array();
    if (!empty($data['current_page']['child_keys'])) {
        foreach ($data['current_page']['child_keys'] as $key => $child) {
            $data['children'][$key] =& $data['pages'][$child];
        }
    }

    // TODO: create a 'siblings' array.
    // Siblings are the children of the current page parent.
    // The root page will have no siblings, as we want to keep this in
    // a single tree.
    // Siblings will include self - filter out in the template if necessary.
    $data['siblings'] = array();
    if (!empty($data['current_page']['parent_key'])) {
        // Loop though all children of the parent.
        foreach ($data['pages'][$data['current_page']['parent_key']]['child_keys'] as $key => $child) {
            $data['siblings'][$key] =& $data['pages'][$child];
        }
    }

    // Provide a 'rolled up' version of the current page (or page and
    // ancestors) that contain inherited values from the pages before it.
    // i.e. all ancestors and the current page layered over each other.
    // TODO: we could save each step here in an array indexed by pid or key - 
    // just have a hunch it would be useful, but not sure how at this stage.
    $inherited = array();
    foreach ($ancestors as $ancestor) {
        $inherited = xarModAPIfunc(
            'xarpages', 'user', 'arrayoverlay',
            array($inherited, $ancestor)
        );
    }
    $data['inherited'] =& $inherited;

    // Add remaining values to the tree.
    $data['pid'] = $pid;

    // Call up a custom function to do any further manipulation or
    // checking, before invoking the rendering template.
    // The function may check page parameters, fetch data etc.
    // The function is placed into the 'xarfuncapi' directory of the module.
    // The function will accept $data as its args, and return the
    // complete args, with any changes or additions.
    // Return values:
    //  NULL    continue processing (the function may not exist)
    //  true    continue processing (the function executed successuly, but did not change the data)
    //  false   stop processing now (the function raised an explicit error)
    //  array   updated data returned (the function changed the data)
    if (!empty($inherited['function'])) {
        // Allow a pipeline of functions (e.g. func1;func2;func3)
        $functions = explode(';', $inherited['function']);
        foreach($functions as $function) {
            // Call up the function, suppressing errors in case it does not exist.
            $data2 = xarModAPIfunc('xarpages', 'func', $function, $data, false);

            // If an array was returned, then assume it contains updated values.
            if (is_array($data2)) {
                $data =& $data2;
            }

            // If the function returned a 'false' then do nothing more.
            // It may have raised an error or set a server redirect.
            if ($data2 === false) {
                return;
            }
        }
    }

    // Set the theme.
    // Use rolled-up page here so the theme is inherited.
    // The special case theme name 'default' will disable this feature
    // and just use the default theme.
    if (!empty($inherited['theme']) && $inherited['theme'] != 'default') {
        xarTplSetThemeName($inherited['theme']);
    }

    // TODO: provide an alternative, configurable, default template, for when none found,
    // ultimately falling back to 'page'. We need to start messing around consuming
    // errors from the error stack to do that though, as xarTplModule() does not have a
    // fallback mechanism to alternative template names (it does shorter template names, but
    // not alternative).

    // Render the module template.
    // Use rolled-up page here so templates are inherited, i.e. so that setting a
    // template on a branch will apply to all pages within that branch, except
    // where sub-branches are explicitly over-ridden.
    $content = xarTplModule('xarpages', 'page', $inherited['pagetype']['name'], $data, $inherited['template']);

    return "$content";
}

?>