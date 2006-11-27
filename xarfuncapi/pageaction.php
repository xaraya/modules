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
 * requires key in post
 *
 * debugging options:
 *  if $debug, will not redirect to next or previous page
 *  if $debug and $dont_execute, user php and functions are not called
*/

require('pageform-helpers.php');

function xarpages_funcapi_pageaction($args)
{
    // incoming post vars
    if (!xarVarFetch('key','str', $key,'',XARVAR_NOT_REQUIRED)) return; 
    
    // STANDARD ARGS
    $pages = $args['pages'];
    $pid = $args['pid'];
    $current_page = $args['current_page'];
    $dd = $args['current_page']['dd'];

    // prev, action (next), and skip  page pid's
    $nav = _pageform_getnav( $args, $key, 1 ); // 1 = this is action page
    $args['pageaction'] = $nav;
    $form_pid = $nav['form_pid'];
    $nextform_pid = $nav['nextform_pid'];
    
    // reuse (append) existing object if one
    if (!empty($key)) {
        $in_object = _pageform_getobject( $key, $pages[$form_pid]['name'] );
    }
    if (empty($in_object)) {
        // create empty one
        $itemtype = $pages[$form_pid]['dd']['data'];
        $in_object = xarModApiFunc('dynamicdata','user','getobject', array('module'=>'dynamicdata', 'itemtype'=>$itemtype ));
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
        $isval = _formaction_validation( $in_object, $dd['validation_php'] );
        if (!$isval) $isvalid = 0;
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
                $isval = $validation_func( $in_object );
                if (!$isval) $isvalid = 0;
            }
        }
    }

    if (!$isvalid) {        
        // save and redirect back to previous page
        _pageform_setobject( $key, $pages[$form_pid]['name'], $in_object );
        if ($dd['debug'] != 1) {
            xarResponseRedirect( $nav['form_url'] );
        }
        // else return
    }
    
    if ($isvalid) { // only not-valid if debugging so we can drop to bottom
        // start an object for next form
        // reuse (append) existing object if one 
        if (!empty($key)) {
            $out_object = _pageform_getobject( $key, $pages[$nextform_pid]['name'] );
        }
        if (empty($out_object)) {
            // create empty one
            $itemtype = $pages[$nextform_pid]['dd']['data'];
            $out_object = xarModApiFunc('dynamicdata','user','getobject', array('module'=>'dynamicdata', 'itemtype'=>$itemtype ));
        }   
    
        // copy any common named values from in to out (especially if they're the same itemtypes!)
        foreach ($in_object->properties as $prop) {
            if (!empty($out_object->properties[$prop->name])) {
                $out_object->properties[$prop->name]->value = $prop->value;
            }
        }
    
        // PROCESSING
        // now call user processing
        /* processing_php : call in database code
        */
        if ($isvalid && !empty($dd['processing_php']) && 
            !($dd['debug'] == 1 && $dd['dont_execute'] == 1)) {
            // now call user validation
            $isvalid = _formaction_processing( $in_object, $out_object, $dd['processing_php'] );
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
            _pageform_setobject( $key, $pages[$form_pid]['name'], $in_object );
            if ($dd['debug'] != 1) {
                xarResponseRedirect( $nav['form_url'] );
            }
            // else return
        }
    }

    if ($isvalid) { // only not-valid if debugging so we can drop to bottom
        // save both in and out objects
        _pageform_setobject( $key, $pages[$form_pid]['name'], $in_object );
        _pageform_setobject( $key, $pages[$nextform_pid]['name'], $out_object );

        // CONTINUE TO PAGE (next form)
        // if not debugging, redirect
        if ($dd['debug'] != 1) {
            xarResponseRedirect( $nav['nextform_url'] );
        }
    }
    // if debugging display this dumy page
    $args['pageaction']['key'] = $key;
    $args['pageaction']['in'] = & $in_object;
    $args['pageaction']['out'] = & $out_object;
    if (!empty($validation_func)) $args['pageaction']['validation_func'] = $validation_func;
    if (!empty($processing_func)) $args['pageaction']['processing_func'] = $processing_func;
    $args['pageaction']['isvalid'] = $isvalid;

    return $args;
}
        
/* eval the validation_php snippet
*/
function _formaction_validation( &$inobj, $php )
{
    // values available to script
    formaction_obj2arrays( $inobj, $values, $invalids );

    // execute the snippet
    $isvalid = eval( $php );
    if (empty($isvalid)) {
        $isvalid = 1;
    }
    // return reslults and double check validation
    $isvalid = $isvalid && formaction_arrays2obj( $values, $invalids, $inobj );
    
    return $isvalid;
}

/* eval the processing_php snippet
*/
function _formaction_processing( &$inobj, &$outobj, $php )
{
    // values available to script
    formaction_obj2arrays( $inobj, $values, $invalids );
    formaction_obj2arrays( $outobj, $outvalues, $outinvalids );

    // execute the snippet
    $isvalid = eval( $php );
    if (empty($isvalid)) {
        $isvalid = 1;
    }
    // return reslults and double check validation
    $isvalid = $isvalid && formaction_arrays2obj( $values, $invalids, $inobj );
    formaction_arrays2obj( $outvalues, $outinvalids, $outobj );

    return $isvalid;
}


?>