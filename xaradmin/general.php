<?php
function window_admin_general()
{
    if (!xarSecurityCheck('AdminWindow')) return;

    if (!xarVarFetch('allow_local_only', 'int', $data['allow_local_only'], xarModGetVar('window', 'allow_local_only'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('use_buffering',    'int', $data['use_buffering'], xarModGetVar('window', 'use_buffering'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('no_user_entry',    'int', $data['no_user_entry'], xarModGetVar('window', 'no_user_entry'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('security',         'int', $data['security'], xarModGetVar('window', 'security'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('reg_user_only',    'int', $data['reg_user_only'], xarModGetVar('window', 'reg_user_only'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('open_direct',      'int', $data['open_direct'], xarModGetVar('window', 'open_direct'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('use_fixed_title',  'int', $data['use_fixed_title'], xarModGetVar('window', 'use_fixed_title'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('auto_resize',      'int', $data['auto_resize'], xarModGetVar('window', 'auto_resize'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('vsize',            'int', $data['vsize'], xarModGetVar('window', 'vsize'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hsize',            'int', $data['hsize'], xarModGetVar('window', 'hsize'), XARVAR_NOT_REQUIRED)) return;
    return $data;
}
?>