<?
/**
 * modify configuration
 */
function googlesearch_admin_modifyconfig()
{
    // Security Check
	if(!xarSecurityCheck('Admingooglesearch')) return;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'googlesearch', array('module' => 'googlesearch'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['createlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>