<?php
/* utility functions for pageform
 *
 * author: jonathan linowes
*/

// Initialisation function, so we can include this file using xarMod::apiFunc()
function xarpages_customapi_pageform_helpers($args)
{
    return true;
}

/* retrieve object from session cache
 * args:
 * $pf - unique session key it was saved under
 * $pagename - name of page containing the object
 * returns:
 * $object - retrieved object, or null if not found
*/  
function _pageform_getobject( $pf, $pagename )
{
    $var = xarSessionGetVar( $pf );

    if (empty($var) || empty($var[$pagename])) {
        return;
    }
    $objectid = $var[$pagename . '_objectid'];
    //$object = xarMod::apiFunc('dynamicdata','user','getobject', array('module'=>'dynamicdata', 'itemtype'=>$itemtype ));
    $object = xarMod::apiFunc('dynamicdata','user','getobject', array('objectid'=>$objectid ));
    $ser = $var[$pagename];
    $vals = unserialize( $ser );
    $object->checkInput($vals); // really just want to do a set value, not validate, oh well TODO:write a loop instead
    $serinvals = $var[$pagename . '_invalids'];
    $invals = unserialize( $serinvals );
    _pageform_setinvalids( $object, $invals ); // should be ->setInvalids() ?
    return $object;
}

/* save an object into session cache
 * just saves the objectid, values, and invalids
 * args:
 * $pf - unique session key to saved under
 * $pagename - name of page containing the object
 * $object - object to save
 * returns:
 * $pf - same as passed in
*/  
function _pageform_setobject( $pf, $pagename, $object )
{
    $vals = $object->getfieldvalues();
    $ser = serialize( $vals );
    
    $invals = _pageform_getinvalids( $object ); // should be ->getInvalids()
    $serinvals = serialize( $invals );
    
    $var = xarSessionGetVar( $pf );
    
    $var[$pagename] = $ser;
    $var[$pagename . '_objectid'] = $object->objectid;
    $var[$pagename . '_invalids'] = $serinvals;

    xarSessionSetVar( $pf, $var );
    
    return $pf;
}

/* unset an object
 * $pf - unique session key to clear
*/
function _pageform_unsetobject( $pf, $pagename )
{
    $var = xarSessionGetVar( $pf );
    if (empty($var)) return;
    unset($var[$pagename]);
    unset($var[$pagename . '_objectid']);
    unset($var[$pagename . '_invalids']);
    xarSessionSetVar( $pf, $var );
    return;
}

/* extract array of 'invalid' strings from object properties
 * args:
 * $object - object
 * returns:
 * $invalids - array of strings: fieldname => invalid
*/  
function _pageform_getinvalids( & $object)
{
    $invalids = array();
    $properties = $object->getProperties();
    foreach ($properties as $property) {
        $invalids[ $property->name ] = $property->invalid;
    }
    return $invalids;
}

/* set 'invalid' strings to object properties
 * args:
 * $object - object
 * $invalids - array of strings: fieldname => invalid
 * returns:
 * none
*/  
function _pageform_setinvalids( & $object, $invals )
{
    foreach ($invals as $name => $invalid ) {
        $object->properties[$name]->invalid = $invalid;
    }
}

/* set 'invalid' strings in object properties to NULL
 * args:
 * $object - object
 * returns:
 * none
*/  
function _pageform_resetinvalids( & $object )
{
    $properties = $object->getProperties();
    foreach ($properties as $property) {
        $object->properties[ $property->name ]->invalid = NULL;
    }
}

/* generate a new random key 
 * args:
 * none
 * returns:
 * $pf
*/
function _pageform_newkey( )
{
    $pf = rand();
    return $pf;
}

/*
 * return nav url's for a given page based on its relative position in sequence
 * assumes pages are alternating form, action, form, action, etc.
 * args:
 * $args - standard xarpages args array given to customapi function (with 'pages', 'pid', etc) 
 * $pf - pageform session key
 * $isaction - type of page, either 0=form, 1=action
 * returns:
 * $nav - array of :
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
 * TODO: skip page inactive and empty pages
*/
function _pageform_getnav( $args, $pf, $isaction = 0 )
{
    $pages = $args['pages']; 
    $currentpid = $args['pid'];

    $keys = array_keys($pages); // i=>pid
    foreach ($keys as $i => $pid) {
        if ($pages[$pid]['status'] != 'ACTIVE') {
            unset($keys[$i]);
        }
    }
    $keys = array_values($keys); // reindex the array
    $indexes = array_flip($keys); // pid=>i

    // cancel is the first page
    $nav['cancel_pid'] = $keys[0];
    $nav['cancel_url'] = _pageform_url( $nav['cancel_pid'] ); // no session key on cancel
    
    // if on first page, and its not a form, just return now
    if ($currentpid == $keys[0]) {
        $firsttype = $pages[$nav['cancel_pid']]['pagetype']['name'];
        if ($firsttype != 'pageform') {
            $nav['nextform_pid'] = $keys[1];
            $nav['nextform_url'] = _pageform_url( $nav['nextform_pid'], $pf);
            return $nav;
        }
    }

    // form is the current form (current page, or prev if we're on the action)
    if ($isaction)
        $nav['form_pid'] = $keys[ $indexes[$currentpid] - 1 ];
    else
        $nav['form_pid'] = $currentpid;
    $nav['form_url'] = _pageform_url( $nav['form_pid'], $pf);
    
    // action is the next page (or current if we're on the action)
    if ($isaction)
        $nav['action_pid'] = $currentpid;
    else
        $nav['action_pid'] = $keys[ $indexes[$currentpid] + 1 ];
    $nav['action_url'] = _pageform_url( $nav['action_pid'], $pf);

    // back is the previous form (or cancel if none)
    $idx = $indexes[ $nav['form_pid'] ] - 2;
    if ($idx < 0)
        $idx = 0;
    $nav['back_pid'] = $keys[$idx];
    $nav['back_url'] = _pageform_url( $nav['back_pid'], $pf);
    
    // nextform (skip) is the next form (null if none)
    $idx = $indexes[ $nav['form_pid'] ] + 2;
    if (!empty($keys[$idx])) {
        $nav['nextform_pid'] = $keys[$idx];
        $nav['nextform_url'] = _pageform_url( $nav['nextform_pid'], $pf);
    }

    return $nav;
}

/* generate a url for pid and pf
 * args:
 * $pid - pid of page
 * $pf - session key (or null)
 * returns:
 * url
*/
function _pageform_url( $pid, $pf = NULL)
{
    if (!empty($pf))
        $url = xarModUrl('xarpages','user','display',array('pid'=>$pid, 'pf'=>$pf));
    else
        $url = xarModUrl('xarpages','user','display',array('pid'=>$pid));
    return $url;
}

/* retrieve values and invalids arrays from object
 * args:
 * $obj - object with properties to extract
 * return args:
 * $values - array containing fieldname => value (array will be initialized)
 * $invalids - array containing fieldname => invalid (array will be initialized)
 * return:
 * $isvalid - 0 if any fields are invalid, else 1
 * 04.04.07 JDJ: $obj is an in-only variable.
*/
function pageform_obj2arrays($obj, &$values, &$invalids)
{
    $isvalid = 1;
    $values = array();
    $invalids = array();

    foreach ($obj->properties as $prop) {
        $values[$prop->name] = $prop->value;
        $invalids[$prop->name] = $prop->invalid;
        if (!empty($invalids[$prop->name])) $isvalid = 0;
    }

    return $isvalid;
}

/* stuff values and invalids arrays into object 
* args:
* $values - array containing fieldname => value
* $invalids - array containing fieldname => invalid
* return args:
* $obj - object with properties set (passed by reference)
* return:
* $isvalid - 0 if any fields are invalid, else 1
 * 04.04.07 JDJ: $values and $invalids are in-only variables.
*/
function pageform_arrays2obj($values, $invalids, &$obj)
{
    $isvalid = 1;

    foreach ($obj->properties as $prop) {
        $obj->properties[$prop->name]->value = $values[$prop->name];
        $obj->properties[$prop->name]->invalid = $invalids[$prop->name];
        if (!empty($invalids[$prop->name])) $isvalid = 0;
    }

    return $isvalid;
}

?>