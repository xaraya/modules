<?php
/**
 * List modules and current settings
 * @param several params from the associated form in template
 *
 */
function censor_admin_settings()
{
    // Security Check
    if(!xarSecurityCheck('EditCensor')) return;
    if (!xarVarFetch('selstyle', 'str:1:', $selstyle, 'plain', XARVAR_NOT_REQUIRED)) return; 
    xarModSetVar('censor', 'selstyle', $selstyle);
    xarResponseRedirect(xarModURL('censor', 'admin', 'view'));
    return true;
}
?>