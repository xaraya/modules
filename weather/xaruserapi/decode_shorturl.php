<?php
function weather_userapi_decode_shorturl(&$params) 
{	
    $args = array();
    
    // if we don't have a function, call the default view
    if(empty($params[1])) {
        return array('main', $args);
    } 
    
    if($params[1] == 'current') {
        $func = 'cc';
        if(isset($params[2]) && $params[2] == 'details') {
           $func = 'ccDetails';
        } elseif(isset($params[2])) {
            // this should be a location
            $args['xwloc'] = $params[2];
        }
        if(isset($params[3])) {
            // this should be a location
            $args['xwloc'] = $params[3];
        }
    } elseif($params[1] == 'details') {
        $func = 'details';
        if(isset($params[2])) {
            // this should be a location
            $args['xwloc'] = $params[2];
        }
        if(isset($params[3])) {
            // this should be a location
            $args['xwloc'] = $params[3];
        }    
    } elseif($params[1] == 'search') {
        $func = 'search';
        if(isset($params[2])) {
            // this should be a location
            $args['xwloc'] = $params[2];
        } 
    } elseif($params[1] == 'modify') {
        $func = 'modifyconfig'; 
        if(isset($params[2])) {
            // this should be a location
            $args['xwloc'] = $params[2];
        } 
    }
    
    // return the decoded information
    return array($func,$args);

}
?>