<?php

include_once('modules/sitesearch/xarclass/xapian.php');

function sitesearch_adminapi_index($args)
{
    extract($args);
    
    if( !isset($database) ){ return false; }
    
    ini_set('max_execution_time', 60*10); // 60 seconds times n minutes
    ini_set('memory_limit', '16M');
    // We do too much work to debug log. So only logg major stuff.
    foreach ( $GLOBALS['xarLog_loggers'] as $id => $logger )
    {
        $GLOBALS['xarLog_loggers'][$id]->_logLevel = XARLOG_LEVEL_ERROR;
    }
    
    $s = microtime(true);   
   
    $engine = new xapian(array());
    $databases = $engine->get_limits();

    foreach( $databases as $db )
    {
        if( $database == $db['database_name'] || $database == 'all' )
        {
            $include_file = 
                "modules/sitesearch/xarclass/{$db['indexer_type']}_indexer.php"; 
            if( file_exists($include_file) )
            {
                include_once($include_file);
                
                $this_indexer = $db['indexer_type'] . '_indexer';
                $indexer = new $this_indexer($db);
                
                $indexer->get_items();
                $indexer->process();
                
                unset($indexer);
            }
        }
    }
    
    $e = microtime(true);    
    //var_dump($e - $s);
    
    return true;
}
?>