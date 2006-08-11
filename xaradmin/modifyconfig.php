<?php
/**
 *
 * Function purpose to be added
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Gmaps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  to be added
 *
 * @param to be added
 * @return to be added
 *
 */

function gmaps_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminGmaps')) return;
    if (!xarVarFetch('phase',     'str:1:100', $phase,       'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab',       'str:1:100', $data['tab'], 'gmaps_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule,   'gmaps', XARVAR_NOT_REQUIRED)) return;

    $hooks = xarModCallHooks('module', 'getconfig', 'gmaps');
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
			if (!xarVarFetch('gmapskey', 'str:1', $gmapskey, xarModGetVar('gmaps', 'gmapskey'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
			if (!xarVarFetch('longitude', 'str:1', $centerlongitude, xarModGetVar('gmaps', 'centerlongitude'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('latitude', 'str:1', $centerlatitude, xarModGetVar('gmaps', 'centerlatitude'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('zoomlevel', 'int:1', $zoomlevel, xarModGetVar('gmaps', 'zoomlevel'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('mapwidth', 'int:1', $mapwidth, xarModGetVar('gmaps', 'mapwidth'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('mapheight', 'int:1', $mapheight, xarModGetVar('gmaps', 'mapheight'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('glargemapcontrol', 'checkbox', $glargemapcontrol, xarModGetVar('gmaps', 'glargemapcontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('gsmallmapcontrol', 'checkbox', $gsmallmapcontrol, xarModGetVar('gmaps', 'gsmallmapcontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('gsmallzoomcontrol', 'checkbox', $gsmallzoomcontrol, xarModGetVar('gmaps', 'gsmallzoomcontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('gscalecontrol', 'checkbox', $gscalecontrol, xarModGetVar('gmaps', 'gscalecontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('gmaptypecontrol', 'checkbox', $gmaptypecontrol, xarModGetVar('gmaps', 'gmaptypecontrol'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('goverviewmapcontrol', 'checkbox', $goverviewmapcontrol, xarModGetVar('gmaps', 'goverviewmapcontrol'), XARVAR_NOT_REQUIRED)) return;

			if ($data['tab'] == 'gmaps_general') {
				xarModVars::set('gmaps', 'SupportShortURLs', $shorturls);
				xarModVars::set('gmaps', 'gmapskey', $gmapskey);
				xarModVars::set('gmaps', 'zoomlevel', $zoomlevel);
				xarModVars::set('gmaps', 'centerlongitude', $centerlongitude);
				xarModVars::set('gmaps', 'centerlatitude', $centerlatitude);
				xarModVars::set('gmaps', 'mapwidth', $mapwidth);
				xarModVars::set('gmaps', 'glargemapcontrol', $glargemapcontrol);
				xarModVars::set('gmaps', 'gsmallmapcontrol', $gsmallmapcontrol);
				xarModVars::set('gmaps', 'gsmallzoomcontrol', $gsmallzoomcontrol);
				xarModVars::set('gmaps', 'gscalecontrol', $gscalecontrol);
				xarModVars::set('gmaps', 'gmaptypecontrol', $gmaptypecontrol);
				xarModVars::set('gmaps', 'goverviewmapcontrol', $goverviewmapcontrol);
			}
			$regid = xarModGetIDFromName($tabmodule);
			xarModSetUserVar('gmaps', 'zoomlevel', $zoomlevel, $regid);
			xarModSetUserVar('gmaps', 'centerlongitude', $centerlongitude, $regid);
			xarModSetUserVar('gmaps', 'centerlatitude', $centerlatitude, $regid);
			xarModSetUserVar('gmaps', 'mapwidth', $mapwidth, $regid);
			xarModSetUserVar('gmaps', 'mapheight', $mapheight, $regid);
			xarModSetUserVar('gmaps', 'glargemapcontrol', $glargemapcontrol, $regid);
			xarModSetUserVar('gmaps', 'gsmallmapcontrol', $gsmallmapcontrol, $regid);
			xarModSetUserVar('gmaps', 'gsmallzoomcontrol', $gsmallzoomcontrol, $regid);
			xarModSetUserVar('gmaps', 'gscalecontrol', $gscalecontrol, $regid);
			xarModSetUserVar('gmaps', 'gmaptypecontrol', $gmaptypecontrol, $regid);
			xarModSetUserVar('gmaps', 'goverviewmapcontrol', $goverviewmapcontrol, $regid);

            xarResponseRedirect(xarModURL('gmaps', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
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
