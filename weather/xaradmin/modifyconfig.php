<?php

function weather_admin_modifyconfig()
{
    $partner_id = xarModGetVar('weather','partner_id');
    $license_key = xarModGetVar('weather','license_key');
    $default_location = xarModGetVar('weather','default_location');
    $units = xarModGetVar('weather','units');
    $extdays = xarModGetVar('weather','extdays');
    $cache_dir = xarModGetVar('weather','cache_dir');
    $cc_cache_time = xarModGetVar('weather','cc_cache_time');
    $ext_cache_time = xarModGetVar('weather','ext_cache_time');
    
    return array(
        'partner_id'=>$partner_id,
        'license_key'=>$license_key,
        'default_location'=>$default_location,
        'cache_dir'=>$cache_dir,
        'cc_cache_time'=>($cc_cache_time/60),
        'ext_cache_time'=>($ext_cache_time/60/60),
        'units'=>$units,
        'extdays'=>$extdays
        );
}

?>