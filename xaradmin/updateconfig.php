<?php
/**
 * Purpose of file
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */

/**
 * @author Roger Raymond
 */
function weather_admin_updateconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminWeather')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    switch ($data['tab']) {
        case 'general':
            if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('weather', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('weather', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('weather', 'aliasname'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('partner_id', 'str', $partner_id,  xarModVars::get('weather', 'partner_id'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('license_key', 'str', $license_key,  xarModVars::get('weather', 'license_key'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('units', 'str', $units,  xarModVars::get('weather', 'units'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('extdays', 'str', $extdays,  xarModVars::get('weather', 'extdays'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('cc_cache_time', 'str', $cc_cache_time,  xarModVars::get('weather', 'cc_cache_time'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('ext_cache_time', 'str', $ext_cache_time,  xarModVars::get('weather', 'ext_cache_time'), XARVAR_NOT_REQUIRED)) return;

            sys::import('modules.dynamicdata.class.properties.base');
            $location = DataPropertyMaster::getProperty(array('name' => 'citylocation'));
            $isvalid = $location->checkInput('default_location');
            if ($isvalid) {
                xarModVars::set('weather','default_location',$location->value);
            }

            xarModVars::set('weather', 'itemsperpage', $itemsperpage);
            xarModVars::set('weather', 'SupportShortURLs', $shorturls);
            xarModVars::set('weather', 'useModuleAlias', $useModuleAlias);
            xarModVars::set('weather', 'aliasname', $aliasname);

            xarModVars::set('weather','partner_id',$partner_id);
            xarModVars::set('weather','license_key',$license_key);
            xarModVars::set('weather','units',$units);
            xarModVars::set('weather','extdays',$extdays);
            xarModVars::set('weather','cc_cache_time',$cc_cache_time);
            xarModVars::set('weather','ext_cache_time',$ext_cache_time);
            break;
        default:
            break;
    }

    xarResponseRedirect(xarModURL('weather', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
    // Return
    return true;
}

?>