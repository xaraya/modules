<?php

/**

*/
function sitesearch_admin_updateconfig($args)
{
    extract($args);

    // Security check
    if( !xarSecurityCheck('AdminSiteSearch') ){ return; }
    
    if( !xarSecConfirmAuthKey() ){ return false; }

    if( !xarVarFetch('itemsperpage',  'int', $ItemsPerPage, 10) ){ return false; }
    if( !xarVarFetch('hlbeg',         'str', $HLBeg, '') ){ return false; }
    if( !xarVarFetch('hlend',         'str', $HLEnd, '') ){ return false; }   
    
    if( !xarVarFetch('qtrack',        'int', $QTrack, 0) ){ return false; }
    if( !xarVarFetch('qcache',        'int', $QCache, 0) ){ return false; }
    if( !xarVarFetch('database_path', 'str', $db_path, 'var/sitesearch/databases') ){ return false; }
    
    // Get config vars
    xarModSetVar('sitesearch', 'ItemsPerPage', $ItemsPerPage);
    xarModSetVar('sitesearch', 'HLBeg', $HLBeg);
    xarModSetVar('sitesearch', 'HLEnd', $HLEnd);    
    
    xarModSetVar('sitesearch', 'QTrack', $QTrack);
    xarModSetVar('sitesearch', 'QCache', $QCache);
    xarModSetVar('sitesearch', 'database_path', $db_path);
        
    xarModCallHooks('module','updateconfig','sitesearch',
        array('module'   => 'sitesearch')
    ); 
    
    xarResponseRedirect(xarModURL('sitesearch', 'admin', 'modifyconfig'));    
    
    return true;
}
?>
