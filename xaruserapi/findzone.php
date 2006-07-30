<?php

/**
 * Find the zone that applies for a particular timezone and timestamp
 *
 * @param $args['timezone'] string the timezone we're looking for
 * @param $args['timestamp'] integer the time period we're interested in
 * @return object zone object that applies
 */
function &timezone_userapi_findzone($args=array())
{
    extract($args);
    if(!isset($timezone) || empty($timezone)) {
        $timezone = 'Etc/UTC';
    }
    if(!isset($timestamp) || empty($timestamp)) {
        $timestamp = time();
    }

    $timezoneData = xarModAPIFunc('timezone','user','gettimezonedata',
                                   array('timezone' => $timezone));

    $year = gmdate('Y',$timestamp);
    $lastyear = 0;

    foreach ($timezoneData as $zone) {
        if (isset($zone->year) && ($year <= $zone->year) && ($zone->year > $lastyear)) {
            $lastyear = $zone->year;
            $lastzone = $zone;
        } elseif (!isset($zone->year) && $lastyear == 0) {
            $lastzone = $zone;
        }
    }
    if (!isset($lastzone)) {
        return;
    }
    return $lastzone;
}

?>