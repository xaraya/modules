<?php

/**
 * Update configuration
 */
function changelog_admin_updateconfig()
{ 
    // Get parameters
    $changelog = xarVarCleanFromInput('changelog');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminChangeLog')) return; 

    if (isset($changelog) && is_array($changelog)) {
        foreach ($changelog as $modname => $value) {
            if ($modname == 'default') {
                xarModSetVar('changelog', 'default', $value);
            } else {
                xarModSetVar('changelog', $modname, $value);
            } 
        } 
    } 

    xarResponseRedirect(xarModURL('changelog', 'admin', 'modifyconfig'));

    return true;
}

?>
