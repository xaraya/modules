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

    // Get the root page for the current page tree.
    // The current page may be a root page, so check that first.
    // TODO: allow relevent privileges to overlook the status.
    if ($current_page['parent'] == 0) {
        $root_page = $current_page;
    } else {
        $root_page = xarModAPIfunc(
            'xarpages', 'user', 'getpage',
            array(
                'wrap_range' => $current_page['left'],
                'parent' => 0,
                'status' => 'ACTIVE'
            )
        );
    }

    // Get the complete tree for this section of pages.
    $data = xarModAPIfunc(
        'xarpages', 'user', 'getpagestree',
        array(
            'left_range' => array($root_page['left'], $root_page['right']),
            'dd_flag' => true,
            'key' => 'pid'
        )
    );

    // Create an ancestors array.
    // We don't do that in getpagestree() since that function does not
    // have any knowledge of the 'current' page.
    // TODO: stop at an non-ACTIVE page. Non-ACTIVE pages act as blockers
    // in the hierarchy.
    $ancestors = array();
    $pid_ancestor = $pid;
    while ($data['pages'][$pid_ancestor]['parent'] <> 0) {
        array_unshift($ancestors, &$data['pages'][$pid_ancestor]);
        $pid_ancestor = $data['pages'][$pid_ancestor]['parent'];
    }
    array_unshift($ancestors, &$data['pages'][$pid_ancestor]);
    $data['ancestors'] = $ancestors;

    // Provide a 'rolled up' version of the current page (or page and
    // ancestors) that contain inherited values from the pages before it.
    // i.e. all ancestors and the current page layered over each other.
    // TODO: we could save each step here in an array indexed by pid or key - 
    // just have a hunch it would be useful, but not sure how at this stage.
    $rolled = array();
    foreach ($ancestors as $ancestor) {
        $rolled = xarModAPIfunc(
            'xarpages', 'user', 'arrayoverlay',
            array($rolled, $ancestor)
        );
    }
    $data['rolled'] =& $rolled;

    // Add remaining values to the tree.
    $data['pid'] = $pid;
    $data['current_page'] =& $data['pages'][$pid];

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
    if (!empty($rolled['function'])) {
        // Allow a pipeline of functions (e.g. func1;func2;func3)
        $functions = explode(';', $rolled['function']);
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
    if (!empty($rolled['theme']) && $rolled['theme'] != 'default') {
        xarTplSetThemeName($rolled['theme']);
    }

    // TODO: provide an alternative, configurable, default template, for when none found,
    // ultimately falling back to 'page'. We need to start messing around consuming
    // errors from the error stack to do that though, as xarTplModule() does not have a
    // fallback mechanism to alternative template names (it does shorter template names, but
    // not alternative).

    // If no template has been defined for this page, or inherited by this page,
    // then set it to 'default'. This should normally be an error page of some sort.
    if ($rolled['template'] == '') {
        $rolled['template'] = 'default';
    }

    // Render the module template.
    // Use rolled-up page here so templates are inherited, i.e. so that setting a
    // template on a branch will apply to all pages within that branch, except
    // where sub-branches are explicitly over-ridden.
    $content = xarTplModule('xarpages', 'page', $rolled['template'], $data);

    return "$content";
}

?>