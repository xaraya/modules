<?php

/**
 * Update configuration
 */
function workflow_admin_updateconfig()
{ 
    // Get parameters
    xarVarFetch('settings','isset',$settings,'', XARVAR_DONT_SET);
    xarVarFetch('isalias','isset',$isalias,'', XARVAR_DONT_SET);
    xarVarFetch('numitems','isset',$numitems,20, XARVAR_DONT_SET);

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return; 

    if (isset($settings) && is_array($settings)) {
        foreach ($settings as $name => $value) {
            xarModSetVar('workflow', $name, $value);
        } 
    } 
    if (empty($isalias)) {
        xarModSetVar('workflow','SupportShortURLs',0);
    } else {
        xarModSetVar('workflow','SupportShortURLs',1);
    }
    if (empty($numitems) || !is_numeric($numitems)) {
        xarModSetVar('workflow','itemsperpage',20);
    } else {
        xarModSetVar('workflow','itemsperpage',$numitems);
    }

    xarResponseRedirect(xarModURL('workflow', 'admin', 'modifyconfig'));

    return true;
}

?>
