<?php
function window_adminapi_updateurlsettings($var) {
    if (!xarSecurityCheck('AdminWindow')) return;

    if (!xarVarFetch('reg_user_only', 'int', $reg_user_only, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('open_direct', 'int', $open_direct, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('use_fixed_title', 'int', $use_fixed_title, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('auto_resize', 'int', $auto_resize, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('vsize', 'int', $vsize, 0, XARVAR_NOT_REQUIRED)) return;
    extract($var);

    //Save Settings
    xarModSetVar('window', 'reg_user_only', $reg_user_only);
    xarModSetVar('window', 'open_direct', $open_direct);
    xarModSetVar('window', 'use_fixed_title', $use_fixed_title);
    xarModSetVar('window', 'auto_resize', $auto_resize);
    xarModSetVar('window', 'vsize', $vsize);

    xarResponseRedirect(xarModURL('window', 'admin', 'addurl'));
}
?>