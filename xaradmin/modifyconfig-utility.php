<?php
/**
 *
 * Function purpose to be added
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Foo Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  to be added
 *
 * @param to be added
 * @return to be added
 *
 */

// Use this version of the modifyconfig file when creating utility modules

function foo_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminFoo')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'foo_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'foo', XARVAR_NOT_REQUIRED)) return;
    $hooks = xarModCallHooks('module', 'getconfig', 'foo');
	if (!empty($hooks) && isset($hooks['tabs'])) {
		foreach ($hooks['tabs'] as $key => $row) {
			$configarea[$key]  = $row['configarea'];
			$configtitle[$key] = $row['configtitle'];
			$configcontent[$key] = $row['configcontent'];
		}
		array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
	} else {
		$hooks['tabs'] = array();
	}
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'foo_general':
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
			if (!xarVarFetch('itemsperpage', 'str:1:4:', $itemsperpage, '20', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
			if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('foo', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('foo', 'aliasname'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('bar', 'str:1', $bar, 'Bar', XARVAR_NOT_REQUIRED)) return;

            if ($data['tab'] == 'foo_general') {
				xarModVars::set('foo', 'itemsperpage', $itemsperpage);
				xarModVars::set('foo', 'supportshorturls', $shorturls);
				xarModVars::set('foo', 'useModuleAlias', $useModuleAlias);
				xarModVars::set('foo', 'aliasname', $aliasname);
				xarModVars::set('foo', 'bar', $bar);
            }
			$regid = xarModGetIDFromName($tabmodule);
			xarModSetUserVar('foo', 'bar', $bar, $regid);

            xarResponseRedirect(xarModURL('foo', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
            // Return
            return true;
            break;

    }
	$data['hooks'] = $hooks;
	$data['tabmodule'] = $tabmodule;
	$data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>
