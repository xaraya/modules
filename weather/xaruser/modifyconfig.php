<?php
function weather_user_modifyconfig()
{
    $default_location = xarModGetUserVar('weather','default_location');
    $units = xarModGetUserVar('weather','units');
    $extdays = xarModGetUserVar('weather','extdays');
    
    return array(
        'default_location'=>$default_location,
        'units'=>$units,
        'extdays'=>$extdays
        );
}
?>