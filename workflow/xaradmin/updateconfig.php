<?php

/**
 * Update configuration
 */
function workflow_admin_updateconfig()
{ 
    // Get parameters
    xarVarFetch('settings','isset',$settings,'', XARVAR_DONT_SET);
    xarVarFetch('isalias','isset',$isalias,'', XARVAR_DONT_SET);

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

    xarResponseRedirect(xarModURL('workflow', 'admin', 'modifyconfig'));

    return true;
}

?>
