<?php

/**
 * Update configuration
 */
function keywords_admin_updateconfig()
{ 
    // Get parameters
    xarVarFetch('restricted','isset',$restricted,'', XARVAR_DONT_SET);
    xarVarFetch('keywords','isset',$keywords,'', XARVAR_DONT_SET);
    xarVarFetch('isalias','isset',$isalias,'', XARVAR_DONT_SET);

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminKeywords')) return; 

    if (isset($keywords) && is_array($keywords)) {
        foreach ($keywords as $modname => $value) {
            if ($modname == 'default') {
                xarModSetVar('keywords', 'default', $value);
            } else {
                xarModSetVar('keywords', $modname, $value);
            } 
        } 
    } 
    if (empty($isalias)) {
        xarModSetVar('keywords','SupportShortURLs',0);
    } else {
        xarModSetVar('keywords','SupportShortURLs',1);
    }
    if (empty($restricted)) {
        xarModSetVar('keywords','restricted',0);
    } else {
        xarModSetVar('keywords','restricted',1);
    }

    xarResponseRedirect(xarModURL('keywords', 'admin', 'modifyconfig'));

    return true;
}

?>
