<?php
function weather_user_main()
{
    // this should be set to return the default view
    return xarModFunc('weather','user','cc');
}

function weather_user_cc()
{
    $w =& xarModAPIFunc('weather','user','factory');
    return array(
        'wData'=>$w->ccData(),
        'eData'=>$w->forecastData()
        );
}
function weather_user_details()
{
    xarVarFetch('xwday','int:0:9',$xwday,0);
    $w =& xarModAPIFunc('weather','user','factory');
    return array(
        'day'=>$xwday,
        'wData'=>$w->ccData(),
        'eData'=>$w->forecastData()
        );
}
?>