<?php

/**
 * Update configuration
 */
function xslt_admin_updateconfig()
{ 
    // Get parameters
    if (!xarVarFetch('xsl','isset',$xsl,array(),XARVAR_NOT_REQUIRED)) return;
    if (!xarSecConfirmAuthKey()) return; 
    if (!xarSecurityCheck('AdminXSLT')) return; 
    if (isset($xsl) && is_array($xsl)) {
        foreach ($xsl as $modname => $value) {
            if ($modname == 'default') {
                xarModSetVar('xslt', 'default', $value);
            } else {
                xarModSetVar('xslt', $modname, $value);
            } 
        } 
    } 
    xarResponseRedirect(xarModURL('xslt', 'admin', 'modifyconfig'));
    return true;
}
?>