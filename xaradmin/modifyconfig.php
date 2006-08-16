<?php
/**
 *
 * Function purpose to be added
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  to be added
 *
 * @param to be added
 * @return to be added
 *
 */

function maps_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminMaps')) return;
    if (!xarVarFetch('phase',     'str:1:100', $phase,       'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab',       'str:1:100', $data['tab'], 'maps_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule,   'maps', XARVAR_NOT_REQUIRED)) return;

    $hooks = xarModCallHooks('module', 'getconfig', 'maps');
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
                case 'general':
                    break;
                default:
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
			if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('mapskey', 'str:1', $mapskey, xarModVars::get('maps', 'mapskey'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
			if (!xarVarFetch('longitude', 'str:1', $centerlongitude, xarModVars::get('maps', 'centerlongitude'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('latitude', 'str:1', $centerlatitude, xarModVars::get('maps', 'centerlatitude'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('zoomlevel', 'int:1', $zoomlevel, xarModVars::get('maps', 'zoomlevel'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('mapwidth', 'int:1', $mapwidth, xarModVars::get('maps', 'mapwidth'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('mapheight', 'int:1', $mapheight, xarModVars::get('maps', 'mapheight'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('glargemapcontrol', 'checkbox', $glargemapcontrol, xarModVars::get('maps', 'glargemapcontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('gsmallmapcontrol', 'checkbox', $gsmallmapcontrol, xarModVars::get('maps', 'gsmallmapcontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('gsmallzoomcontrol', 'checkbox', $gsmallzoomcontrol, xarModVars::get('maps', 'gsmallzoomcontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('gscalecontrol', 'checkbox', $gscalecontrol, xarModVars::get('maps', 'gscalecontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('gmaptypecontrol', 'checkbox', $gmaptypecontrol, xarModVars::get('maps', 'gmaptypecontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('goverviewmapcontrol', 'checkbox', $goverviewmapcontrol, xarModVars::get('maps', 'goverviewmapcontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('uselocations', 'array', $uselocations, array(), XARVAR_NOT_REQUIRED)) return;

			if ($data['tab'] == 'maps_general') {
				xarModVars::set('maps', 'SupportShortURLs', $shorturls);
				xarModVars::set('maps', 'mapskey', $mapskey);
				xarModVars::set('maps', 'zoomlevel', $zoomlevel);
				xarModVars::set('maps', 'centerlongitude', $centerlongitude);
				xarModVars::set('maps', 'centerlatitude', $centerlatitude);
				xarModVars::set('maps', 'mapwidth', $mapwidth);
				xarModVars::set('maps', 'glargemapcontrol', $glargemapcontrol);
				xarModVars::set('maps', 'gsmallmapcontrol', $gsmallmapcontrol);
				xarModVars::set('maps', 'gsmallzoomcontrol', $gsmallzoomcontrol);
				xarModVars::set('maps', 'gscalecontrol', $gscalecontrol);
				xarModVars::set('maps', 'gmaptypecontrol', $gmaptypecontrol);
				xarModVars::set('maps', 'goverviewmapcontrol', $goverviewmapcontrol);
				xarModVars::set('maps', 'uselocations', serialize($uselocations));
			}
			$regid = xarModGetIDFromName($tabmodule);
			xarModSetUserVar('maps', 'zoomlevel', $zoomlevel, $regid);
			xarModSetUserVar('maps', 'centerlongitude', $centerlongitude, $regid);
			xarModSetUserVar('maps', 'centerlatitude', $centerlatitude, $regid);
			xarModSetUserVar('maps', 'mapwidth', $mapwidth, $regid);
			xarModSetUserVar('maps', 'mapheight', $mapheight, $regid);
			xarModSetUserVar('maps', 'glargemapcontrol', $glargemapcontrol, $regid);
			xarModSetUserVar('maps', 'gsmallmapcontrol', $gsmallmapcontrol, $regid);
			xarModSetUserVar('maps', 'gsmallzoomcontrol', $gsmallzoomcontrol, $regid);
			xarModSetUserVar('maps', 'gscalecontrol', $gscalecontrol, $regid);
			xarModSetUserVar('maps', 'gmaptypecontrol', $gmaptypecontrol, $regid);
			xarModSetUserVar('maps', 'goverviewmapcontrol', $goverviewmapcontrol, $regid);
			xarModVars::set('maps', 'uselocations', serialize($uselocations), $regid);

            xarResponseRedirect(xarModURL('maps', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
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
