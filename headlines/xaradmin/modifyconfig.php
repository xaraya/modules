<?php
/**
 * modify configuration
 */
function headlines_admin_modifyconfig()
{
    // Security Check
	if(!xarSecurityCheck('AdminHeadlines')) return;

    $hooks = xarModCallHooks('module', 'modifyconfig', 'headlines', array('module' => 'headlines'));
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
