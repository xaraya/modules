<?php

/* place this file into xarfuncapi/ directory
 *
 * custom page function for 'pageform' page type
 *
 * adds $pageform variable to the page, an array of:
 *  'pf' - unique session key
 *  'object' - current object for the page form (either new or from session cache)

 *  'form_pid' - pid of current form page (current page, or if this is pageaction the previous page)
 *  'form_url' - url of current form page
 *  'action_pid' - pid of current action page (next page, or if this pageaction the current page)
 *  'action_url' - url of current action page
 *  'cancel_pid' - pid of cancel page (first page)
 *  'cancel_url' - url of cancel page
 *  'back_pid' - pid of previous form (or cancel if none)
 *  'back_url' - url of previous form
 *  'nextform_pid' - pid of next form page
 *  'nextform_url' - url of next form page
 *
*/

function xarpages_funcapi_pageform($args)
{
    // Include the helper functions
    xarMod::apiFunc('xarpages', 'custom', 'pageform_helpers');

    // incoming pageform key
    if (!xarVarFetch('pf','str', $pf,'',XARVAR_NOT_REQUIRED)) return;

    // STANDARD ARGS
    $pages = $args['pages'];
    $pid = $args['pid'];
    $current_page = $args['current_page'];
    $dd = $args['current_page']['dd'];

    // make reference key
    if (empty($pf)) {
        if (isset($dd['unique_key']) && $dd['unique_key']) {
            $pf = _pageform_newkey();
            // (guaranteed a new cache)
            // TODO: somewhere have to clean out any old pf objects
        }
        else {
            $pf = xarUserGetVar('uid');
            // clear cached object
            _pageform_unsetobject( $pf, $current_page['name'] );
        }
    }    
    
    // prev, action (next), and skip  page pid's
    $args['pageform'] = _pageform_getnav( $args, $pf );
    
    // resuse (append) existing object if one
    $object = _pageform_getobject( $pf, $current_page['name'] );

    // reset values with user function
    if (!empty($object) && !empty($dd['reset_php']) && !empty($dd['always_reset']) && $dd['always_reset'] ) {
        // now call user validation
        $isvalid = _pageform_reset( $object, $dd['reset_php'] );
        // TO DO: reset_func
    }

    if (empty($object)) {
        // create empty one
        //$object = xarMod::apiFunc('dynamicdata','user','getobject', array('module'=>'dynamicdata', 'itemtype'=>$dd['data'] ));
        $object = xarMod::apiFunc('dynamicdata','user','getobject', array('objectid'=>$dd['data'] ));

        // reset values with user function
        if (!empty($dd['reset_php'])) {
            // now call user validation
            $isvalid = _pageform_reset( $object, $dd['reset_php'] );
            // TO DO: reset_func
        }
    }   
    
    // required fields, eg in case javascripts want it
    $requiredarr= array();
/*
    if (!empty($dd['required'])) {
        $required = explode(',', $dd['required']);
        foreach ($required as $key=>$name) {
            $name = trim($name); // just in case
            if (isset($object->properties[$name])) {
                $requiredarr[$name] = & $object->properties[$name];
            }
        }
    }   
*/
    // default submit button
    if (empty($dd['submit_label'])) {
        $args['current_page']['dd']['submit_label'] = 'Submit';
    }

    $args['pageform']['pf'] = $pf;
    $args['pageform']['object'] = & $object;
    $args['pageform']['properties'] = & $object->properties;
    $args['pageform']['required'] = $requiredarr;

    return $args;
}

/* eval the reset_php snippet
*/
function _pageform_reset( &$inobj, $php )
{
    // values available to script
    pageform_obj2arrays( $inobj, $values, $invalids );

    // execute the snippet (ignore return value)
    eval( $php );

    // return reslults and double check validation
    pageform_arrays2obj( $values, $invalids, $inobj );
    
    return 1;
}

?>