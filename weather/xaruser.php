<?php
function weather_user_main()
{
    // this should be set to return the default view
    return xarModFunc('weather','user','cc');
}

function weather_user_cc()
{
    $w =& xarModAPIFunc('weather','user','factory');
    return array('wData'=>$w->ccData());
}

function weather_user_ccDetails()
{
    $w =& xarModAPIFunc('weather','user','factory');
    return array('wData'=>$w->ccData());
}

function weather_user_extForecast()
{
    $w =& xarModAPIFunc('weather','user','factory');
    return array('wData'=>$w->forecastData());
}

function weather_user_extForecastDetails()
{
    $w =& xarModAPIFunc('weather','user','factory');
    return array('wData'=>$w->forecastData());
}
?>