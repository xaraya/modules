<?php

include_once('modules/sitesearch/xarclass/xapian.php');

/**
    Main Search Function
*/
function sitesearch_user_main()
{
    if( !extension_loaded('xapian') ) {
        $msg = "SiteSearch requires PHP5+ with the Xapian php extension";
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'XAR_PHP_EXCEPTION', $msg);
        return false;
    }
    
    if( !xarVarFetch('keywords', 'str', $keywords, '') ){ return false; }
    if( !xarVarFetch('startnum', 'int', $startNum, 1) ){ return false; }
    if( !xarVarFetch('database', 'str', $database, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('prefixes', 'array', $prefixes, null, XARVAR_NOT_REQUIRED) ){ return false; }
        
    $databases = null;
    if( !empty($database) )
        $databases[]['database_name'] = $database;

    $engine = new xapian_engine($databases);
    
    // If we have keywords let do the search
    if( !empty($keywords) )
    {
        xarModAPILoad('sitesearch', 'user');

        // Perform the search
        $start_time = microtime(true);
        $result = $engine->search($keywords, $startNum-1);
        $end_time = microtime(true);
        
        // Retreive details
        $totalPages   = $engine->get_doc_count();
        $totalMatches = $engine->get_num_matches();
        xarLogMessage("SS: Found $totalPages documents with $totalMatches matches");
        $hlbeg = xarModGetVar('sitesearch', 'HLBeg');
        $hlend = xarModGetVar('sitesearch', 'HLEnd');

        // Process Results
        $data = array();

        // Setup the Pager
        $url = xarModURL('sitesearch', 'user', 'main',
            array(
                'startnum'   => '%%', 
                'keywords'   => $keywords,
                'database'   => $database
            )
        );
        $itemsPerPage  = xarModGetVar('sitesearch', 'itemsperpage');
        if( empty($itemsPerPage) ){ $itemsPerPage = 10; } 
        $data['pager'] = xarTplGetPager( $startNum, $totalMatches, $url, $itemsPerPage );
        
        // Prepare vars for template
        $data['results']      = $engine->results;        
        $data['totalMatches'] = $totalMatches;
        $data['searchTime']   = number_format($end_time - $start_time, 4);
        $data['firstRow']     = $startNum;
        $data['lastRow']      = $startNum + $engine->num_results - 1;
        $data['search']       = true;
    }

    $data['totalPages'] = $engine->get_doc_count();
    
    $data['keywords'] = htmlentities($keywords);
    $data['database'] = isset($database) ? $database : '';
    $data['databases'] = $engine->get_limits();

    // Return completed page
    return $data;
}
?>