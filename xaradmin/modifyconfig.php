<?php
/**
 * Purpose of file
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */

/**
 * @author Marc Lutolf
 */

    function weather_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurityCheck('AdminWeather')) return;
        if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'weather'));
        $data['module_settings']->setFieldList('items_per_page, use_module_alias, use_module_icons');
        $data['module_settings']->getItem();

        switch (strtolower($phase)) {
            case 'modify':
            default:
                switch ($data['tab']) {
                    case 'general':
                        if (!xarVarFetch('default_location', 'str', $data['default_location'],  xarModVars::get('weather', 'default_location'), XARVAR_NOT_REQUIRED)) return;
                        break;
                    default:
                        break;
                }

                break;

            case 'update':
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
    
                xarModVars::set('weather','partner_id',$partner_id);
                xarModVars::set('weather','license_key',$license_key);
                xarModVars::set('weather','units',$units);
                xarModVars::set('weather','extdays',$extdays);
                xarModVars::set('weather','cc_cache_time',$cc_cache_time);
                xarModVars::set('weather','ext_cache_time',$ext_cache_time);

                $isvalid = $data['module_settings']->checkInput();
                if (!$isvalid) {
                    return xarTpl::module('dynamicdata','admin','modifyconfig', $data);
                } else {
                    $itemid = $data['module_settings']->updateItem();
                }

                xarController::redirect(xarModURL('weather', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
                break;

        }
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>
