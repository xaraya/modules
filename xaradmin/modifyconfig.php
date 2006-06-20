<?php
function window_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminWindow')) return;

    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

	switch ($data['tab']) {
		case 'general':
			break;
		case 'display':
			// this is sort of stupid, but I don't think there is a better way at present
		    $info = xarModGetInfo(3002);
		    if (!xarVarFetch('showusermenu','int', $data['showusermenu'],$info['usercapable'],XARVAR_NOT_REQUIRED)) return;
			break;
		default:
			break;
	}
	$data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>