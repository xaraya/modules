<?php

/**
 * Update configuration
 */
function userpoints_admin_updateconfig()
{ 
    // Get parameters
    if(!xarVarFetch('score',    'isset', $score,    10, XARVAR_NOT_REQUIRED)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminUserpoints')) return; 

    // Update default style
    if (!is_array($score)) {
        xarModSetVar('userpoints', 'defaultscore', $score);
    } else {
        foreach ($score as $modname => $value) {
            if ($modname == 'default') {
                xarModSetVar('userpoints', 'defaultscore', $value);
            } else {
                xarModSetVar('userpoints', 'points.' . $modname, $value);
            } 
        } 
    } 

    xarResponseRedirect(xarModURL('userpoints', 'admin', 'modifyconfig'));

    return true;
} 

?>
