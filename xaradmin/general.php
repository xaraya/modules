<?php
function window_admin_general()
{
    if (!xarSecurityCheck('AdminWindow')) return;

    $data = array();
    $data['allow_local_only'] = xarModGetVar('window', 'allow_local_only');
    $data['use_buffering'] = xarModGetVar('window', 'use_buffering');
    $data['no_user_entry'] = xarModGetVar('window', 'no_user_entry');
    $data['security'] = xarModGetVar('window', 'security');
    $data['reg_user_only'] = xarModGetVar('window', 'reg_user_only');
    $data['open_direct'] = xarModGetVar('window', 'open_direct');
    $data['use_fixed_title'] = xarModGetVar('window', 'use_fixed_title');
    $data['auto_resize'] = xarModGetVar('window', 'auto_resize');
    $data['vsize'] = xarModGetVar('window', 'vsize');
    $data['hsize'] = xarModGetVar('window', 'hsize');

    return $data;
}
?>