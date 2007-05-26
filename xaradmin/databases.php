<?php
/**
    Manages various limit options
*/
function sitesearch_admin_databases()
{
    // Security check
    if (!xarSecurityCheck('AdminSiteSearch')) return; 

    xarVarFetch('update', 'str', $update, '');
    
    if( $update )
    {
        xarVarFetch('limits', 'str', $limits, '');
    
        xarModSetVar('sitesearch', 'databases', $limits);
    }

    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['limits'] = xarModGetVar('sitesearch', 'databases');

    return $data;
}
?>