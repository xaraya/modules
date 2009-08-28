<?php

/* place this file into xarfuncapi/ directory
 *
 * custom page function for 'pageaction' page type
 *
 * this is called as result of a submit
 * - puts POST vars into object, 
 * - checks input (built-in validation)
 * - check required fields
 * - validate form (user php)
 * - validate form (user function)
 * - if invalid, go back
 * - process form (user php)
 * - process form (user function)
 * - if ok, save data object and go to next page
 *
 * requires pf in post
 *
 * debugging options:
 *  if $debug, will not redirect to next or previous page
 *  if $debug and $dont_execute, user php and functions are not called
 *
 * Bugs: crashes if the next page always be a pageform with a data object defined, even if you dont use it
*/

function xarpages_funcapi_pageaction($args)
{
    // Include the helper functions
    xarModAPIfunc('xarpages', 'custom', 'pageform_helpers');

    // incoming post vars
    if (!xarVarFetch('pf','str', $pf,'',XARVAR_NOT_REQUIRED)) return; 

    // STANDARD ARGS
    $pages = $args['pages'];
    $pid = $args['pid'];
    $current_page = $args['current_page'];
    $dd = $args['current_page']['dd'];

    // prev, action (next), and skip  page pid's
    $nav = _pageform_getnav( $args, $pf, 1 ); // 1 = this is action page
    $args['pageaction'] = $nav;
    $form_pid = $nav['form_pid'];
    if (!empty($nav['nextform_pid'])) {
    $nextform_pid = $nav['nextform_pid'];
    }
    // make reference key
    if (empty($pf)) {
        if (isset($dd['unique_key']) && $dd['unique_key'])
            $pf = _pageform_newkey();
        else
            $pf = xarUserGetVar('uid');
    }    
    // reuse (append) existing object if one
    $in_object = _pageform_getobject( $pf, $pages[$form_pid]['name'] );

    if (empty($in_object)) {
        // create empty one
        $objectid = $pages[$form_pid]['dd']['data'];
        $in_object = xarModApiFunc('dynamicdata','user','getobject', array('objectid'=>$objectid ));
    }
    else {
        // clear invalids, we'll be checking them again now
        _pageform_resetinvalids( $in_object );
    }   
    if (empty($in_object)) {
        // error
        // should redirect back to form
        // if debugging, show it in this page (and template provide a back button)
        $args['message'] = 'error: no object';
        return $args;
    }
    
    // VALIDATION
    // check the input values using xar validation
    $isvalid = $in_object->checkInput();

    // check for required fields
    if (($isvalid || !empty($dd['batch_validations'])) && 
            !empty($pages[$form_pid]['dd']['required']))
    {
        $required = explode(',', $pages[$form_pid]['dd']['required']);
        foreach ($required as $name) {
            $name = trim($name); // just in case
            if (!empty($in_object->properties[$name]) && empty($in_object->properties[$name]->value) && empty($in_object->properties[$name]->invalid)) {
                $in_object->properties[$name]->invalid = "Required field"; // ML this, and/or make it configurable
                $isvalid = false;
            }
        }
    }
    // call user validation php if:
    //      validation php exists, AND
    //      isvalid, or we're batching the validations, AND
    //      we're executing (in debug mode)
    if (($isvalid || !empty($dd['batch_validations'])) && 
            !empty($dd['validation_php']) &&
            !($dd['debug'] == 1 && $dd['dont_execute'] == 1)) 
    {
        // now call user validation
        $isvalid = _pageform_validation( $in_object, $dd['validation_php'] );
    }
        
    /* validation_function field contains name of the function
        first we load the file containing the functions (aka library :)
        named xarpages/xarcustomapi/PAGENAME.php with function xarpages_customapi_PAGENAME
        Then the processing function is called pageform_PAGENAME_FUNCNAME
    */
    // call user validation func if:
    //      validation function specified and exists, AND
    //      isvalid, or we're batching the validations, AND
    //      we're executing (in debug mode)
    if (($isvalid  || !empty($dd['batch_validations'])) && 
            !empty($dd['validation_func'])) 
    {
        $validation_func = 'pageform_'.$current_page['name'].'_'.$dd['validation_func'];

        if (!($dd['debug'] == 1 && $dd['dont_execute'] == 1)) {
            // load the functions (library)
            xarModApiFunc('xarpages','custom',$current_page['name'] );      
            if (function_exists($validation_func)) {
                $isvalid = $validation_func( $in_object );
            }
        }
    }

    if (!$isvalid) {        
        // save and redirect back to previous page
        _pageform_setobject( $pf, $pages[$form_pid]['name'], $in_object );
        if ($dd['debug'] != 1) {
            xarResponse::Redirect( $nav['form_url'] );
        }
        // else return
    }
    
    if ($isvalid) { // only not-valid if debugging so we can drop to bottom
        // start an object for next form (if one)
        if (!empty($nextform_pid) && !empty($pages[$nextform_pid])) {
            // reuse (append) existing object if one 
            if (!empty($pf)) {
                $out_object = _pageform_getobject( $pf, $pages[$nextform_pid]['name'] );
            }
            if (empty($out_object)) {
                // create empty one
                $objectid = $pages[$nextform_pid]['dd']['data'];
                //$out_object = xarModApiFunc('dynamicdata','user','getobject', array('module'=>'dynamicdata', 'itemtype'=>$itemtype ));
                $out_object = xarModApiFunc('dynamicdata','user','getobject', array('objectid'=>$objectid ));
            }   
    
            // copy any common named values from in to out (especially if they're the same object ids!)
            foreach ($in_object->properties as $prop) {
                if (isset($out_object->properties[$prop->name])) {
                    $out_object->properties[$prop->name]->value = $prop->value;
                }
            }
            _pageform_resetinvalids( $out_object );
        }

        // PROCESSING
        // now call user processing
        /* processing_php : call in database code
        */
        if ($isvalid && !empty($dd['processing_php']) && 
            !($dd['debug'] == 1 && $dd['dont_execute'] == 1)) {
            // now call user validation
            $isvalid = _pageform_processing( $in_object, $out_object, $dd['processing_php'] );
        }
    
        /* processing_function field contains name of the function
            first we load the file containing the functions (aka library :)
            named xarpages/xarcustomapi/PAGENAME.php with function xarpages_customapi_PAGENAME
            Then the processing function is called pageform_PAGENAME_FUNCNAME
        */
        if (!empty($dd['processing_func'])) { // get full name now even if debugging so we can dump it
            $processing_func = 'pageform_'.$current_page['name'].'_'.$dd['processing_func'];
        }
        if ($isvalid && !empty($dd['processing_func']) &&
            !($dd['debug'] == 1 && $dd['dont_execute'] == 1)) {
            // load the functions (library)
            xarModApiFunc('xarpages','custom',$current_page['name'] );      
            if (function_exists($processing_func)) {
                $isvalid = $processing_func( $in_object, $out_object );
            }
        }

        // processing can also prove invalid
        if (!$isvalid) {        
            // save input and redirect back to previous page (current form)
            _pageform_setobject( $pf, $pages[$form_pid]['name'], $in_object );
            if ($dd['debug'] != 1) {
                xarResponse::Redirect( $nav['form_url'] );
            }
            // else return
        }
    }

    if ($isvalid) { // only not-valid if debugging so we can drop to bottom
        // save both in and out objects
        _pageform_setobject( $pf, $pages[$form_pid]['name'], $in_object );
        if (!empty($out_object) && !empty($nextform_pid) && !empty($pages[$nextform_pid]['name'])) {
            _pageform_setobject( $pf, $pages[$nextform_pid]['name'], $out_object );
        }

        // CONTINUE TO PAGE (next form)
        // if not debugging, redirect
        if ($dd['debug'] != 1) {
            if (!empty($dd['redirect_nav'])) {
                xarResponse::Redirect( $nav[$dd['redirect_nav']] );
            }
            else {
                xarResponse::Redirect( $nav['nextform_url'] );
            }
        }
    }
    // if debugging display this dumy page
    $args['pageaction']['pf'] = $pf;
    $args['pageaction']['in'] = & $in_object;
    $args['pageaction']['out'] = & $out_object;
    if (!empty($validation_func)) $args['pageaction']['validation_func'] = $validation_func;
    if (!empty($processing_func)) $args['pageaction']['processing_func'] = $processing_func;
    $args['pageaction']['isvalid'] = $isvalid;
    $args['pageaction']['nav'] = $nav;
    
    return $args;
}
        
/* eval the validation_php snippet
*/
function _pageform_validation( &$inobj, $php )
{
    // values available to script
    pageform_obj2arrays( $inobj, $values, $invalids );

    // execute the snippet
    $isvalid = eval( $php );
    if (empty($isvalid)) {
        $isvalid = 1;
    }
    // return reslults and double check validation
    $isvalid = $isvalid && pageform_arrays2obj( $values, $invalids, $inobj );
    
    return $isvalid;
}

/* eval the processing_php snippet
*/
function _pageform_processing( &$inobj, &$outobj, $php )
{
    // values available to script
    pageform_obj2arrays( $inobj, $values, $invalids );
    pageform_obj2arrays( $outobj, $outvalues, $outinvalids );

    // execute the snippet
    $isvalid = eval( $php );
    if (empty($isvalid)) {
        $isvalid = 1;
    }
    // return reslults and double check validation
    $isvalid = $isvalid && pageform_arrays2obj( $values, $invalids, $inobj );
    pageform_arrays2obj( $outvalues, $outinvalids, $outobj );

    return $isvalid;
}

?>