<?php
//$Id:$

function timezone_userapi_encode_shorturl(&$params) 
{
	// Get arguments from argument array
    //extract($args); unset($args);
    // check if we have something to work with
    if (!isset($params['func'])) { return; }
    
	// default path is empty -> no short URL
    $path = '';
    $extra = '';
    // we can't rely on xarModGetName() here (yet) !
    $module = 'timezone';
    switch($params['func']) {
        
        case 'main':
            $path = "/$module/";
            break;
            
        case 'settimezone':
            $path = "/$module/set/";
            
        
    }
    
    return $path;

}

?>
