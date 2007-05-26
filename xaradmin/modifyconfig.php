<?php

/**
    Modfy Site Search config options.
*/
function sitesearch_admin_modifyconfig($args)
{
    // Security check
    if (!xarSecurityCheck('AdminSiteSearch')) return; 
    
    extract($args);
    
    $data = array();
    
    $data['authid'] = xarSecGenAuthKey();
    
    // Get config vars
    $data['ItemsPerPage']  = xarModGetVar('sitesearch', 'ItemsPerPage');
    $data['HLBeg']         = xarVarPrepForDisplay(xarModGetVar('sitesearch', 'HLBeg'));
    $data['HLEnd']         = xarVarPrepForDisplay(xarModGetVar('sitesearch', 'HLEnd'));
    
    $data['QTrack']        = xarModGetVar('sitesearch', 'QTrack');
    $data['QCache']        = xarModGetVar('sitesearch', 'QCache');
    
    $data['database_path'] = xarModGetVar('sitesearch', 'database_path');
    
    // Process Hooks
    $hooks = xarModCallHooks('module', 'modifyconfig', 'sitesearch',
        array('module' => 'sitesearch')
    );
    if (empty($hooks)) 
    {
        $data['hooks'] = array();
    } 
    else 
    {
        $data['hooks'] = $hooks;
    } 
                                         
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration')); 
    
    return $data;
}
?>