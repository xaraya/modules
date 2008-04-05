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
    // grab the following form variables
    xarVarFetch('partner_id','str::',$partner_id,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('license_key','str::',$license_key,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('default_location','str::',$default_location,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('cc_cache_time','int::',$cc_cache_time,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('ext_cache_time','int::',$ext_cache_time,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('units','str::',$units,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('extdays','int::',$extdays,null,XARVAR_NOT_REQUIRED);
    
    
    if(isset($partner_id)) {
        xarModVars::set('weather','partner_id',$partner_id);
    }
    
    if(isset($license_key)) {
        xarModVars::set('weather','license_key',$license_key);
    }
    
    if(isset($default_location)) {
        xarModVars::set('weather','default_location',$default_location);
    }
    
    if(isset($units)) {
        xarModVars::set('weather','units',$units);
    }
    
    if(isset($extdays)) {
        xarModVars::set('weather','extdays',$extdays);
    }
    
    if(isset($cc_cache_time)) {
        if($cc_cache_time >= 30) {
            $cc_cache_time *= 60; // time coming in is in minutes
            xarModVars::set('weather','cc_cache_time',$cc_cache_time);
        } else {
            $msg = xarML('Current Conditions Cache time isn\'t long enough') . xarML('Please enter a Cache Time longer than or equal to 30 minutes for the Current Conditions Cache Time.');
            throw new Exception($msg);
        }
    }
    
    if(isset($ext_cache_time)) {
        if($ext_cache_time >= 2) {
            $ext_cache_time *= (60*60); // time coming in is in hours
            xarModVars::set('weather','ext_cache_time',$ext_cache_time);
        } else {
            $msg = xarML('Extended Forecast Cache time isn\'t long enough') . xarML('Please enter a Cache Time longer than or equal to 2 hours for the Extended Forecast Cache Time.');
            throw new Exception($msg);
        }
    }
    
    // set the shorturl support (only do this on FORM submit)
    xarVarFetch('shorturls','int:0:1',$shorturls,0);
    xarModVars::set('weather','SupportShortURLs',$shorturls);
    
    xarResponseRedirect(xarModURL('weather','admin','modifyconfig'));
}

?>