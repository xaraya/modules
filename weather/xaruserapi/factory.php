<?php
function &weather_userapi_factory()
{
    static $xwobj;
    if (!isset($xwobj)) {
        include_once('modules/weather/xoapWeather.php');
        $xwobj =& new xoapWeather();
    }
    return $xwobj;
}
?>