<?php
function foo_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminFoo')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'general':
                    break;
                case 'tab2':
                    break;
                case 'tab3':
                    break;
                default:
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
            switch ($data['tab']) {
                case 'general':
                    if (!xarVarFetch('itemsperpage', 'str:1:4:', $itemsperpage, '20', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;

                    xarModSetVar('foo', 'itemsperpage', $itemsperpage);
                    xarModSetVar('foo', 'SupportShortURLs', $shorturls);
                    break;
                case 'tab2':
                    break;
                case 'tab3':
                    break;
                default:
                    break;
            }

            xarResponseRedirect(xarModURL('foo', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
            // Return
            return true;
            break;

    }
	$data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>
