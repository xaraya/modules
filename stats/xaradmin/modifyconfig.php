<?php

/**
 * modify configuration
 */
function stats_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminStats')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'update':
            if (!xarVarFetch('countadmin', 'checkbox', $countadmin, false, XARVAR_NOT_REQUIRED)) return;
            // the following call returns an empty startdate, so it will be set wrong later on
            // if (!xarVarFetch('startdate', 'str:1', $startdate, '', XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return; 
            // Update module variables
            xarModSetVar('stats', 'countadmin', $countadmin);
            // xarModSetVar('stats', 'startdate', $startdate);

            xarResponseRedirect(xarModURL('stats', 'admin', 'modifyconfig')); 
            // Return
            return true;

            break;
        case 'modify':
        default: 
            
            // Quick Data Array
            $data['authid'] = xarSecGenAuthKey();
            $data['updatelabel'] = xarML('Update Users Configuration');

            break;
    } 

    return $data;
} 

?>