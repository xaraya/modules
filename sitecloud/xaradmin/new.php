<?php
function sitecloud_admin_new()
{
    // Security Check
	if(!xarSecurityCheck('Addsitecloud')) return;
    $item = array();
    $item['module'] = 'sitecloud';
    $item['itemtype'] = NULL; // forum
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['submitlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey();
    // Return the output
    return $data;
}
?>
