<?php

/**
 * multiform helper API.
 *
 * All functions are held in this one API for simplicity and clarity.
 */

/*
 * Get the master page for the current page.
 */ 

function xarpages_multiformapi_getmasterpage($args)
{
    // Save some time by caching the master page.
    // It won't change through the life of one page rendering.
    static $master_page = NULL;
    if (!empty($master_page)) return $master_page;

    $master_page_type_name = 'multiform_master';

    // Get the ancestor tree.
    if (empty($args['current_page']['pidpath'])) return;
    $pidpath = $args['current_page']['pidpath'];

    // We need to walk up through the ancestors to find a page of type 'multiform_master'
    // We start at the end of the pidpath array

    $masterpage_pid = 0;

    if (empty($args['pages'])) return;
    while(!empty($pidpath)) {
        $pid = array_pop($pidpath);
        $pagetype = $args['pages'][$pid]['pagetype']['name'];
        if ($pagetype == $master_page_type_name) {
            // Found it.
            $masterpage_pid = $pid;
            break;
        }
    }

    if (!empty($masterpage_pid)) {
        // Found one.
        $master_page = $args['pages'][$masterpage_pid];
        // TODO: interpret a few of the parameters the page holds, and provide
        // some defaults, then add those parameters to the page data.
        // Parameters include: timeout, debug, timeoutpage, errorpage, cancelpage, showhistory
        //
        // 'showhistory' determines the sequence. If set, then earlier pages can be re-accessed in random order.
        // If not set, then order is strictly along the history line, one page at a time. The 'direction' flag
        // tells us whether the direction is one way, or whether the user can go back and amend earlier forms.
        // 'showhistory' is more than just a crumb-trail display - it is used to ensure the right sequence is adhered to.

        // Get a list of descendant pages that make up the form sequence.
        // This is a linearised list of descendants of the master page, arranged left-to-right.
        $page_sequence = array();
        foreach($args['pages'] as $pid => $page) {
            if ($page['left'] > $master_page['right']) break;
            if ($page['left'] > $master_page['left'] && $page['status'] == 'ACTIVE' && $page['pagetype']['name'] != 'multiform_exception') {
                $page_sequence[] = $pid;
            }
        }
        $master_page['page_sequence'] = $page_sequence;
        $master_page['first_pid'] = array_shift($page_sequence);
    }

    return $master_page;
}

/*
 * Return a reference to the session variable.
 * If no parameters, then returns the current array, otherwise sets it.
 * Usual use would be to call this API at the start of a function, change the
 * array it returns, then call again at the end to write the changes back.
 * The special value 'reset'=>true will reset the session vars.
 */

function &xarpages_multiformapi_sessionvar($args = array())
{
    // Everything is stored under here, as an associative array.
    $session_var_name = 'xarpages_multiform_sessionvars';

    if (empty($args)) {
        $session_var = xarSessionGetVar($session_var_name);
    } elseif (empty($args['reset'])) {
        $session_var = $args;
        xarSessionSetVar($session_var_name, $session_var);
    }

    // If not set, then make sure there is something there.
    if (empty($session_var) || !empty($args['reset'])) {
        // Set the default array.
        // Each history element, keyed by the pid, will contain:
        // - values for that page
        // - pid
        // - page name
        // - list of invalids (if not empty, then the page is not fully valid)
        //   - or a single flag to indicate whether the page successfuly validated
        // - flag to indicate whether the user is allowed to revisit the page
        // The history array will be ordered in the order in which pages were first encountered.
        // This is important for the history 
        $session_var = array(
            'session_key' => '',
            'history' => array(), // History of pages visited
            'formdata' => array(), // Collected form data from (and shared between) all pages
            'workdata' => array(), // Working data for the process functions; used to pass data between pages
        );
        xarSessionSetVar($session_var_name, $session_var);
    }

    return $session_var;
}


/*
 * Get, set or clear the session key.
 */

function xarpages_multiformapi_sessionkey($args)
{
    // Get the session vars. We just have a reference to it,
    // so can set values if required.
    $session_vars = xarpages_multiformapi_sessionvar();

    // Get the session key from the session.
    $session_key = $session_vars['session_key'];

    // If 'set' is set, then create a new key.
    // If 'reset' is set, then completely remove the key.
    if (!empty($args['set'])) {
        // Create a new random key then store it in the session
        $session_key = md5(rand(1, getrandmax()) . time());
        $session_vars['session_key'] = $session_key;
        xarpages_multiformapi_sessionvar($session_vars);
    } elseif (!empty($args['reset'])) {
        // Clear the key in the session.
        // TODO: this is probably a good point to clear everything in the session,
        // i.e. to clear the whole session_vars array.
        $session_key = NULL;
        $session_vars['reset'] = true;
        xarpages_multiformapi_sessionvar($session_vars);
    }

    // Return the current key.
    return $session_key;
}

/*
 * Get the custom validation object.
 * If the class does not exist, returns NULL.
 * @param name The name of the validation set (name of the master page)
 */

function xarpages_multiformapi_getvalobject($args)
{
    extract($args);

    if (empty($name) || !xarVarValidate('pre:trim:ftoken', $name)) return;

    // Call the object creation factory function.
    // Pass the args through, as it will contain initialisation data for the object.
    // Make sure errors are suppressed for this call, as the API may not exist.
    $object = xarModAPIfunc('xarpages', 'custom', 'multiform_' . $name, $args, false);

    return $object;
}

?>