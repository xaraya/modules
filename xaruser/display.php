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
    // Only fetch active or empty pages.
    // TODO: allow the administrator to display other statuses.
    if (!empty($pid)) {
        $current_page = xarModAPIfunc(
            'xarpages', 'user', 'getpage',
            array('pid' => $pid, 'status' => 'ACTIVE,EMPTY')
        );

        // If no page found, try the 'notfound' page.
        // TODO: If this page exists, we need to pass some additional
        // information to it, so it can render a sensible message or
        // make some choices.
        if (empty($current_page)) {
            $pid = xarModGetVar('xarpages', 'notfoundpage');

            if (!empty($pid)) {
                $current_page = xarModAPIfunc(
                    'xarpages', 'user', 'getpage',
                    array('pid' => $pid, 'status' => 'ACTIVE')
                );
            }
        }
    }

    // Security check on the page and page type.
    // FIXME: this only checks the current page - the
    // tree fetch lower down will determine whether the page
    // has no privilege by virtue of an ancestor.
    $noprivspage = xarModGetVar('xarpages', 'noprivspage');
    if (!empty($current_page) && !xarSecurityCheck(
        'ReadXarpagesPage', (empty($noprivspage) ? 1 : 0), 'Page',
        $current_page['name'] . ':' . $current_page['pagetype']['name'], 'xarpages'
    )) {
        // If we don't have a special page reserved for handling lack of
        // privileges, then return now with a generic error.
        // The security check would have been called up with error handling
        // enabled if there were a 'no privs' page.
        if (empty($noprivspage)) {return;}

        // No privileges to read this page.
        // Direct to a 'no privs' page, so an admin can notify the
        // visitor of restricted areas of the site.
        $current_page = NULL;
        $pid = $noprivspage;

        if (!empty($pid)) {
            $current_page = xarModAPIfunc(
                'xarpages', 'user', 'getpage',
                array('pid' => $pid, 'status' => 'ACTIVE')
            );
        }
    }

    if (empty($current_page) && !empty($pid)) {
        // Get the PID for the 'error' page.
        // We have a pid, but no page, so there must have been an error
        // attempting to fetch the page.
        $pid = xarModGetVar('xarpages', 'errorpage');

        if (!empty($pid)) {
            $current_page = xarModAPIfunc(
                'xarpages', 'user', 'getpage',
                array('pid' => $pid, 'status' => 'ACTIVE')
            );
        }
    }

    if (empty($current_page)) {
        // Give up: we could not find the requested page, the notfound page nor the error page.
        // Return the dafault display template.
        return array();
    }

    // TODO: allow relevent privileges to over-ride the status,
    // i.e. don't assume the site owner only wants to display ACTIVE
    // and EMPTY pages to every level of user.
    // Get the complete tree for this section of pages.
    $data = xarModAPIfunc(
        'xarpages', 'user', 'getpagestree',
        array(
            'tree_contains_pid' => $pid,
            'dd_flag' => true,
            'key' => 'pid',
            'status' => 'ACTIVE,EMPTY'
        )
    );

    // If we don't have permission to display pages within this tree
    // (even if we could fetch the specific page) then return.
    if (empty($data['pages'][$pid])) {
        // Return the dafault display template.
        // TODO: attempt to load the 'noprivs' page then the error page.
        // This is kind of okay to do, since the user should never see a
        // link to this page if it does not appear in any menu.
        // It's too late to think this through now ;-) This will basically
        // involve some kind of loop, attempting to fetch the tree for
        // the current page, then the privs page, falling back to the
        // error page, and finally to array() i.e. no page.
        return array();
    }

    // If the selected page is EMPTY, scan its children to find
    // an ACTIVE child.
    if ($data['pages'][$pid]['status'] == 'EMPTY') {
        if (!empty($data['pages'][$pid]['child_keys'])) {
            foreach($data['pages'][$pid]['child_keys'] as $scan_key) {
                // If the page is displayable, then treat it as the new page.
                if ($data['pages'][$scan_key]['status'] == 'ACTIVE') {
                    $pid = $data['pages'][$scan_key]['pid'];
                    break;
                }
            }
        }
    }

    // Now we can cache all this data away for the blocks.
    // The blocks should have access to most of the same data as the page.
    xarVarSetCached('Blocks.xarpages', 'pagedata', $data);

    // Save the current page ID. This is used by blocks in 'automatic' mode.
    xarVarSetCached('Blocks.xarpages', 'current_pid', $pid);

    // Add in flags etc. to the data indicating where the current
    // page is in relation to the page tree.
    $data = xarModAPIfunc(
        'xarpages', 'user', 'addcurrentpageflags',
        array('pagedata' => $data, 'pid' => $pid)
     );

    // Provide a 'rolled up' version of the current page (or page and
    // ancestors) that contain inherited values from the pages before it.
    // i.e. all ancestors and the current page layered over each other.
    // TODO: we could save each step here in an array indexed by pid or key - 
    // just have a hunch it would be useful, but not sure how at this stage.
    $inherited = array();
    foreach ($data['ancestors'] as $ancestor) {
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
    //  true    continue processing (the function executed successfuly, but did not change the data)
    //  false   stop processing now (the function raised an explicit error)
    //  array   updated data returned (the function changed the data)
    if (!empty($inherited['function'])) {
        // Allow a pipeline of functions (e.g. func1;func2;func3)
        $functions = explode(';', $inherited['function']);
        foreach($functions as $function) {
            // Call up the function, suppressing errors in case it does not exist.
            $data2 = xarModAPIfunc('xarpages', 'func', $function, $data, false);

            if (!isset($data2)) {
                // Try the next function if this one is not set (i.e. NULL)
                continue;
            }

            // If the function returned a 'false' then do nothing more.
            // It may have raised an error or set a server redirect.
            if ($data2 === false) {
                return;
            }

            // If an array was returned, then assume it contains updated values.
            if (is_array($data2)) {
                $data =& $data2;
            }

            // The function returned some other value, e.g. 'true'.
            // These have no significance at the moment. The option
            // remains open to use other values for specific purposes.
            // Carry on processing any further functions.
        }
    }

    // Set the theme.
    // Use rolled-up page here so the theme is inherited.
    // The special case theme name 'default' will disable this feature
    // and just use the default theme.
    if (!empty($inherited['theme']) && $inherited['theme'] != 'default') {
        xarTplSetThemeName($inherited['theme']);
    }

    // Set the page template.
    // Use rolled-up page here so the theme is inherited.
    // The special case theme name 'default' will disable this feature
    // and just use the default theme.
    if (!empty($inherited['page_template']) && $inherited['page_template'] != 'default') {
        xarTplSetPageTemplateName($inherited['page_template']);
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
    return xarTplModule('xarpages', 'page', $inherited['pagetype']['name'], $data, $inherited['template']);
}

?>