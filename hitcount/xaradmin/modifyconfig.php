<?php

/**
 * modify configuration
 */
function hitcount_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminHitcount')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'modify':
        default: 
            
            // Quick Data Array
            $data['authid'] = xarSecGenAuthKey();
            $data['updatelabel'] = xarML('Update Users Configuration');

            break;

        case 'update':
            if (!xarVarFetch('countadmin', 'checkbox', $countadmin, false, XARVAR_NOT_REQUIRED)) return;
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return; 
            // Update module variables
            xarModSetVar('hitcount', 'countadmin', $countadmin);
            xarResponseRedirect(xarModURL('hitcount', 'admin', 'modifyconfig')); 
            // Return
            return true;

            break;
    } 

    return $data;
} 

?>