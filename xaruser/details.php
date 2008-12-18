<?php
function weather_user_details()
{
    xarVarFetch('xwday','int:0:9',$xwday,0);
    xarVarFetch('location','str',$location, xarModVars::get('weather','default_location'));
    $w = xarModAPIFunc('weather','user','factory');
    $w->setLocation($location);
    return array(
        'day'=>$xwday,
        'wData'=>$w->ccData(),
        'eData'=>$w->forecastData()
        );
}
?>
