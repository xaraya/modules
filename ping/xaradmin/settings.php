<?php
/**
 * List modules and current settings
 * @param several params from the associated form in template
 *
 */
function ping_admin_settings()
{
    // Security Check
    if(!xarSecurityCheck('Adminping')) return;
    if (!xarVarFetch('selstyle', 'str:1:', $selstyle, 'plain', XARVAR_NOT_REQUIRED)) return; 
    xarModSetVar('ping', 'selstyle', $selstyle);
    xarResponseRedirect(xarModURL('ping', 'admin', 'view'));
    return true;
}
?>