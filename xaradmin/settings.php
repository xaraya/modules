<?php
/**
 * List modules and current settings
 * @param several params from the associated form in template
 *
 */
function headlines_admin_settings()
{
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;
    if (!xarVarFetch('selstyle', 'str:1:', $selstyle, 'plain', XARVAR_NOT_REQUIRED)) return; 
    xarModSetVar('headlines', 'selstyle', $selstyle);
    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));
    return true;
}
?>