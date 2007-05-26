<?php
/**

*/
function sitesearch_userapi_getlimits($args=array())
{
    extract($args);
    
    $limits['urls'] = array();
    
    $urls = xarModGetVar('sitesearch', 'limits');    
    
    if( empty($urls) )
    	return '';
    	
    $urls = split("\n", $urls);
    foreach( $urls as $url )
    {
        $url = split(",", $url);
        $path = isset($url[0]) ? trim($url[0]) : '';
        $name = isset($url[1]) ? trim($url[1]) : '';
        $limits['urls'][$path] = $name;
    }    
    
    return $limits;
}
?>