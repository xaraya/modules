<?php
function window_admin_updateconfig($var)
{
    if (!xarSecurityCheck('AdminWindow')) return;
    if (!xarVarFetch('default_size',     'int', $default_size, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allow_local_only', 'int', $allow_local_only, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('use_buffering',    'int', $use_buffering, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('no_user_entry',    'int', $no_user_entry, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('security',         'int', $security, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('reg_user_only',    'int', $reg_user_only, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('open_direct',      'int', $open_direct, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('use_fixed_title',  'int', $use_fixed_title, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('auto_resize',      'int', $auto_resize, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('vsize',            'int', $vsize, 600, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hsize',            'int', $hsize, '100%', XARVAR_NOT_REQUIRED)) return;
    extract($var);
    //Save Settings
    xarModSetVar('window', 'default_size', $default_size);
    xarModSetVar('window', 'allow_local_only', $allow_local_only);
    xarModSetVar('window', 'use_buffering', $use_buffering);
    xarModSetVar('window', 'no_user_entry', $no_user_entry);
    xarModSetVar('window', 'security', $security);
    xarModSetVar('window', 'reg_user_only', $reg_user_only);
    xarModSetVar('window', 'open_direct', $open_direct);
    xarModSetVar('window', 'use_fixed_title', $use_fixed_title);
    xarModSetVar('window', 'auto_resize', $auto_resize);
    xarModSetVar('window', 'vsize', $vsize);
    xarModSetVar('window', 'hsize', $hsize);
    //Set Status
    xarResponseRedirect(xarModURL('window', 'admin', 'modifyconfig'));
    return true;
}
?>