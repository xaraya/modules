<?php
/**
 * modify configuration
 */
function sitecloud_admin_modifyconfig()
{
    // Security Check
	if(!xarSecurityCheck('Adminsitecloud')) return;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'sitecloud', array('module' => 'sitecloud'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>
