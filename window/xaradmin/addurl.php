<?php
// Add a Url
// This is the default when you hit this settings page
function window_admin_addurl()
{
    if (!xarSecurityCheck('AdminWindow')) return;
    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['action'] = xarModURL('window', 'admin', 'newurl');
    $data['window_status'] = "add";
    $data['urls'] = xarModAPIFunc('window','admin','geturls');
    $data['id'] = "";
    $data['host'] = "http://";
    $data['alias'] = "";
    $data['reg_user_only'] = xarModGetVar('window', 'reg_user_only');
    $data['open_direct'] = xarModGetVar('window', 'open_direct');
    $data['use_fixed_title'] = xarModGetVar('window', 'use_fixed_title');
    $data['auto_resize'] = xarModGetVar('window', 'auto_resize');
    $data['vsize'] = xarModGetVar('window', 'vsize');
    $data['hsize'] = xarModGetVar('window', 'hsize');
    $data['lang_action'] = xarML('Add');
    return xarTplModule('window','admin','url',$data);
}
?>