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
    // TODO: only do this if we are not already on the master page.

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
            'expires' => 0,
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
        // If we are setting a new session key, then clear out the old session first.
        $session_vars = xarpages_multiformapi_sessionvar(array('reset'=>true));
        
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

/*
 * Pass an array of data from one page to another.
 * This provides a one-off pass in the style of a batton.
 * - Invoking with args will store all the args in the Xaraya session.
 * - Invoking with empty args will return the stored args and remove them from the session
 * This data pass uses a different session variable than the main form sequence, so
 * can be used to pass data out at the end of a sequence (after the sequence session
 * has been cleared).
 */ 

function xarpages_multiformapi_passdata($args = array())
{
    // Everything is stored under here, as an associative array.
    $session_var_name = 'xarpages_multiform_passvars';

    if (empty($args)) {
        // No args, so return whatever is stored
        $args = xarSessionGetVar($session_var_name);
        if (empty($args)) $args = NULL;
        xarSessionDelVar($session_var_name);
    } else {
        // Args supplied, so store it
        xarSessionSetVar($session_var_name, $args);
    }

    return $args;
}

/**
 * Get a propulated form object.
 * The object will be returned, populated with any existing form data.
 * Useful in summary pages where you want to display the content of
 * several forms.
 * Alternatively, the object ID or the module/itemtype can be supplied,
 * as the object may not be used in any pages, and my exist just to display
 * summary information.
 *
 * @param pagename string The name of the page that the object is defined on.
 * @param objectid integer The ID of the object.
 * @return object The DD object, populated with data, or NULL in the event of any error.
 * @todo Support module+itemtype identification of an object
 */

function xarpages_multiformapi_get_object_populated($args)
{
    extract($args);

    // Page name
    if (!empty($pagename)) {
        $page = xarModAPIfunc('xarpages', 'user', 'getpage', array('name' => $pagename));

        // No page found.
        if (empty($page)) return;

        // No object id found.
        if (empty($page['dd']['formobject'])) return;

        $objectid = (int)$page['dd']['formobject'];
    }

    // No object ID, after the above checks.
    if (empty($objectid)) return;

    // Attempt to get the DD object.
    $object = xarModApiFunc(
        'dynamicdata', 'user', 'getobject',
        array('objectid' => $objectid)
    );

    // No object found
    if (empty($object)) return;

    // Populate the object from the session.
    $session_vars = xarModAPIfunc('xarpages', 'multiform', 'sessionvar');
    foreach($object->properties as $name => $property) {
        if (isset($session_vars['formdata'][$name])) $object->properties[$name]->setValue($session_vars['formdata'][$name]);
    }

    return $object;
}

?>