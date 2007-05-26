<?php
/**
    Hook for generating a url from $extrainfo to index
*/
function sitesearch_adminapi_indexpage($args)
{
    extract($args);
    
    /**
        Check to see if we have a useable indexer
    */
    $IndexerPath = xarModGetVar('sitesearch', 'IndexerPath');
    if( empty($IndexerPath) || !file_exists($IndexerPath) )
        return $args;
    
    /**
        Generate the url to generate
    */
    if( !empty($extrainfo['module']) )
        $module = $extrainfo['module'];
        
    $type = 'user';        
    $func = 'display';
    $path = "modules/$module/xar$type/";
    
    if( !file_exists($path . $func . '.php') )
        return $args;
        
    if( isset($extrainfo['itemtype']) )
        $itemtype = $extrainfo['itemtype'];
        
    if( isset($objectid) )
        $itemid = $objectid;        
        
    $uargs = array();    
    if( $module == 'articles' )
    {
        $uargs['ptid'] = $itemtype;
        $uargs['aid'] = $itemid;
    }
    else 
    {
        $uargs['itemtype'] = $itemtype;
        $uargs['itemid'] = $itemid;    
    }
       
    $url = xarModURL($module, $type, $func, $uargs);

    /**
        -i : Inserts new url
        -u : url pattern to insert
        -R : Pop. Ranking
        -n n : Indexer only n documents
    */
    //$output = exec($IndexerPath . ' -a -R -n 1 -i -u ' . $url, $out, $re);

    return $args;
}
?>