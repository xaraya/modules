<?php
// $Id:$


// /calendar/function/Ymd/

function timezone_userapi_decode_shorturl(&$params) 
{	
    $args = array();
    
    // if we don't have a function, call the default view
    if(empty($params[1])) {
        return array('main', $args);
    }
    
    return array($params[1],$args);
}

?>
