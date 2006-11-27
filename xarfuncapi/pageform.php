<?php

/* place this file into xarfuncapi/ directory
 *
 * custom page function for 'pageform' page type
 *
 * adds $pageform variable to the page, an array of:
 *  'key' - unique session key
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

require('pageform-helpers.php');

function xarpages_funcapi_pageform($args)
{
    // incoming pageform key
    if (!xarVarFetch('key','str', $key,'',XARVAR_NOT_REQUIRED)) return;
    if (empty($key)) {
        // key required, make one and restart this page
        $key = _pageform_newkey();
        $url = xarServerGetCurrentURL( array('key'=>$key) );
        xarResponseRedirect( $url );
    }
    
    // STANDARD ARGS
    $pages = $args['pages'];
    $pid = $args['pid'];
    $current_page = $args['current_page'];
    $dd = $args['current_page']['dd'];
    
    // prev, action (next), and skip  page pid's
    $args['pageform'] = _pageform_getnav( $args, $key );
    
    // resuse (append) existing object if one
    if (!empty($key)) {
        $object = _pageform_getobject( $key, $current_page['name'] );
    }

    if (empty($object)) {
        // create empty one
        $object = xarModApiFunc('dynamicdata','user','getobject', array('module'=>'dynamicdata', 'itemtype'=>$dd['data'] ));
    }   
    
    // required fields, eg in case javascripts want it
    if (!empty($dd['required'])) {
        $required = explode(',', $dd['required']);
        foreach ($required as $name) {
            $name = trim($name); // just in case
        }
    }   

    $args['pageform']['key'] = $key;
    $args['pageform']['object'] = & $object;
        
    return $args;
}

?>