<?php
/**
 * List modules and current settings
 * @param several params from the associated form in template
 *
 */
function sitecloud_admin_settings()
{
    // Security Check
    if(!xarSecurityCheck('Editsitecloud')) return;
    if (!xarVarFetch('selstyle', 'str:1:', $selstyle, 'plain', XARVAR_NOT_REQUIRED)) return; 
    xarModSetVar('sitecloud', 'selstyle', $selstyle);
    xarResponseRedirect(xarModURL('sitecloud', 'admin', 'view'));
    return true;
}
?>