<?php

include_once('modules/sitesearch/xarclass/engine.php');

function sitesearch_admin_qtrack($args)
{
    // Security check
    if (!xarSecurityCheck('AdminSiteSearch')) return; 

    extract($args);
    
    xarVarFetch('startnum', 'int', $startNum, 1);
    $itemsPerPage = 20;

    $engine = new sitesearch_engine();    
    
    $data = array();

    $data['NumQueries'] = $engine->count_searches();
    $data['NumQueryWords'] = $engine->count_logs();
    $data['qtracks'] = $engine->get_logs($startNum, $itemsPerPage);

    //Setup the Pager
    $url = xarModURL('sitesearch', 'admin', 'qtrack',
        array('startnum'   => '%%')
    );
    $data['pager'] = xarTplGetPager( $startNum, $data['NumQueryWords'], $url, $itemsPerPage );
    
    
    return $data;
}
?>