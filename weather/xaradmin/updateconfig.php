<?php

function weather_admin_updateconfig()
{
    // grab the following form variables
    xarVarFetch('partner_id','str::',$partner_id,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('license_key','str::',$license_key,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('default_location','str::',$default_location,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('cache_dir','str::',$cache_dir,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('cc_cache_time','int::',$cc_cache_time,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('ext_cache_time','int::',$ext_cache_time,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('units','str::',$units,null,XARVAR_NOT_REQUIRED);
    xarVarFetch('extdays','int::',$extdays,null,XARVAR_NOT_REQUIRED);
    
    
    if(isset($partner_id)) {
        xarModSetVar('weather','partner_id',$partner_id);
    }
    
    if(isset($license_key)) {
        xarModSetVar('weather','license_key',$license_key);
    }
    
    if(isset($default_location)) {
        xarModSetVar('weather','default_location',$default_location);
    }
    
    if(isset($units)) {
        xarModSetVar('weather','units',$units);
    }
    
    if(isset($extdays)) {
        xarModSetVar('weather','extdays',$extdays);
    }
    
    if(isset($cache_dir)) {
        xarModSetVar('weather','cache_dir',$cache_dir);
    }
    
    if(isset($cc_cache_time)) {
        if($cc_cache_time >= 30) {
            $cc_cache_time *= 60; // time coming in is in minutes
            xarModSetVar('weather','cc_cache_time',$cc_cache_time);
        } else {
            xarErrorSet(
                XAR_USER_EXCEPTION,
                xarML('Current Conditions Cache time isn\'t long enough'),
                xarML('Please enter a Cache Time longer than or equal to 30 minutes for the Current Conditions Cache Time.')
                );
        }
    }
    
    if(isset($ext_cache_time)) {
        if($ext_cache_time >= 2) {
            $ext_cache_time *= (60*60); // time coming in is in hours
            xarModSetVar('weather','ext_cache_time',$ext_cache_time);
        } else {
            xarErrorSet(
                XAR_USER_EXCEPTION,
                xarML('Extended Forecast Cache time isn\'t long enough'),
                xarML('Please enter a Cache Time longer than or equal to 2 hours for the Extended Forecast Cache Time.')
                );
        }
    }
    
    // set the shorturl support (only do this on FORM submit)
    xarVarFetch('shorturls','int:0:1',$shorturls,0);
    xarModSetVar('weather','SupportShortURLs',$shorturls);
    
    xarResponseRedirect(xarModURL('weather','admin','modifyconfig'));
}

?>