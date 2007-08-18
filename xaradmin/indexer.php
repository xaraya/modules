<?php
include_once('modules/sitesearch/xarclass/xapian.php');

/*

*/
function sitesearch_admin_indexer($args)
{
    // Security check
    if (!xarSecurityCheck('AdminSiteSearch')) return; 

    if( !xarVarFetch('db', 'str', $db, null, XARVAR_NOT_REQUIRED) ){ return false; }
    extract($args);

    
    if( !is_null($db) )
    {
        // run the indexer for the database
        $result = xarModAPIFunc('sitesearch', 'admin', 'index', array('database' => $db));
    }
        
    $data = array();
    
    $engine = new xapian_engine(array());
    $data['databases'] = $engine->get_limits();

    return xarTplModule('sitesearch', 'admin', 'index', $data);
}
?>
