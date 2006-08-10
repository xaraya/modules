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
                    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('gmapskey', 'str:1', $gmapskey, xarModGetVar('gmaps', 'gmapskey'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    if (!xarVarFetch('longitude', 'str:1', $centerlongitude, xarModGetVar('gmaps', 'centerlongitude'), XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('latitude', 'str:1', $centerlatitude, xarModGetVar('gmaps', 'centerlatitude'), XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('zoomlevel', 'int:1', $zoomlevel, xarModGetVar('gmaps', 'zoomlevel'), XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('mapwidth', 'str:1', $mapwidth, xarModGetVar('gmaps', 'mapwidth'), XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('mapheight', 'int:1', $mapheight, xarModGetVar('gmaps', 'mapheight'), XARVAR_NOT_REQUIRED)) return;

                    xarModSetVar('gmaps', 'SupportShortURLs', $shorturls);
                    xarModSetVar('gmaps', 'gmapskey', $gmapskey);
                    xarModSetVar('gmaps', 'zoomlevel', $zoomlevel);
                    xarModSetVar('gmaps', 'centerlongitude', $centerlongitude);
                    xarModSetVar('gmaps', 'centerlatitude', $centerlatitude);
                    xarModSetVar('gmaps', 'mapwidth', $mapwidth);
                    xarModSetVar('gmaps', 'mapheight', $mapheight);
                    break;
                case 'tab2':
                    break;
                case 'tab3':
                    break;
                default:
                    break;
            }

            xarResponseRedirect(xarModURL('gmaps', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
            // Return
            return true;
            break;

    }
	$data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>
