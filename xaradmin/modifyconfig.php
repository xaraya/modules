<?php

/**
 * modify configuration
 */
function opentracker_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminOpentracker')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'modify':
        default: 
            // Quick Data Array
            $data = array();            
            break;

        case 'update':
            if (!xarVarFetch('countadmin', 'checkbox', $countadmin, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('trackoutgoing', 'checkbox', $trackoutgoing, false, XARVAR_NOT_REQUIRED)) return;
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return; 
            // Update module variables
            xarModSetVar('opentracker', 'countadmin', $countadmin);
            xarModSetVar('opentracker', 'trackoutgoing', $trackoutgoing);
            xarResponseRedirect(xarModURL('opentracker', 'admin', 'modifyconfig')); 
            // Return
            return true;

            break;
    } 

    return $data;
} 

?>
