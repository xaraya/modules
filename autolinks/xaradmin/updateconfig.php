<?php

/**
 * update configuration
 */
function autolinks_admin_updateconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminAutolinks')) return;

    if (!xarSecConfirmAuthKey()) return;

    // The flags that are accepted (values: 0 or 1; 'name'=>default-value)
    // TODO: this list will expand with advanced options.
    $flags = array('newwindow'=>0, 'nbspiswhite'=>0);

    // Deal with flags.
    foreach ($flags as $flag => $default)
    {
        unset($flagvalue);
        xarVarFetch($flag, 'int:0:1', $flagvalue, $default, XARVAR_NOT_REQUIRED);
        xarModSetVar('autolinks', $flag, $flagvalue);
    }

    if (!xarVarFetch('maxlinkcount', 'int:1:', $maxlinkcount, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemsperpage', 'int:1:', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('decoration', 'str::30', $decoration, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('punctuation', 'str::30', $punctuation, '', XARVAR_NOT_REQUIRED)) {return;}

    xarModSetVar('autolinks', 'itemsperpage', $itemsperpage);
    xarModSetVar('autolinks', 'maxlinkcount', $maxlinkcount);
    xarModSetVar('autolinks', 'decoration', $decoration);
    xarModSetVar('autolinks', 'punctuation', $punctuation);

    xarResponseRedirect(xarModURL('autolinks', 'admin', 'modifyconfig'));

    return true;
}

?>
