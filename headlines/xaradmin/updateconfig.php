<?php
/**
 * update configuration
 */
function headlines_admin_updateconfig()
{
    $itemsperpage = xarVarCleanFromInput('itemsperpage');

    if (!xarSecConfirmAuthKey()) return;

    // Security Check
	if(!xarSecurityCheck('AdminHeadlines')) return;

    if (!isset($itemsperpage)) {
        $itemsperpage = 20;
    }

    xarModSetVar('headlines', 'itemsperpage', $itemsperpage);
    xarModCallHooks('module','updateconfig','headlines', array('module' => 'headlines'));
    xarResponseRedirect(xarModURL('headlines', 'admin', 'modifyconfig'));

    return true;
}
?>
