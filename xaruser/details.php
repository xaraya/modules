<?php
function weather_user_details()
{
    xarVarFetch('xwday','int:0:9',$xwday,0);
    $w = xarModAPIFunc('weather','user','factory');
    return array(
        'day'=>$xwday,
        'wData'=>$w->ccData(),
        'eData'=>$w->forecastData()
        );
}
?>