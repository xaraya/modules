<?php

/**
 * Update configuration
 */
function xslt_admin_updateconfig()
{ 
    // Get parameters
    $xsl = xarVarCleanFromInput('xsl');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
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
