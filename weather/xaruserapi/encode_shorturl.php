<?php
function weather_userapi_encode_shorturl(&$params) 
{
	// Get arguments from argument array
    if (!isset($params['func'])) { return; }
    
    // default path is empty -> no short URL
    $path = '';
    $extra = '';
    
    // we can't rely on xarModGetName() here (yet) !
    $module = 'weather';
    
    // specify some short URLs relevant to your module
    switch($params['func']) {
        case 'main':
            $path = "/$module/";
            break;
        
        case 'cc':
            $path = "/$module/current/";
            break;
            
        case 'ccdetails':
            $path = "/$module/current/details/";
            break;
            
        case 'extforecast':
            $path = "/$module/extended/";
            break;
            
        case 'extforecastdetails':
            $path = "/$module/extended/details/";
            break;
            
        case 'search':
            $path = "/$module/search/";
            break;
            
        case 'modifyconfig':
            $path = "/$module/modify/";
            break;
    }
    if(isset($params['xwloc'])) {
        $path .= $params['xwloc'].'/';
    }
    if(!empty($path) && isset($params['xwunits'])) {
        $join = empty($extra) ? '?' : '&amp;';
        $extra .= $join . 'xwunits=' . $params['xwunits'];
    }
    return $path.$extra;
}

?>