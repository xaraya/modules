<?php

/**
 * Get timezone information
 *
 * @param $args['timezone'] string the timezone we're looking for
 * @param $args['timestamp'] integer the time period we're interested in
 * @return array
 * @returns array(tz offset, dst start, dst end, dst adjust, std code, dst code)
 */
function timezone_userapi_tzinfo($args=array())
{
    extract($args);
    if(!isset($timezone) || empty($timezone)) {
        $timezone = 'Etc/UTC';
    }
    if(!isset($timestamp) || empty($timestamp)) {
        $timestamp = time();
    }
    
    $zone =& xarModAPIFunc('timezone','user','findzone',
                           array('timezone'  => $timezone,
                                 'timestamp' => $timestamp));
    if (!isset($zone)) {
        return array();
    }

    $offset = $zone->gmtoff;
    $format = $zone->format;

    $info = xarModAPIFunc('timezone','user','findrules',
                          array('rules'     => $zone->rules,
                                'offset'    => $zone->gmtoff,
                                'timestamp' => $timestamp));
    if (empty($info)) {
        return array($offset);
    }
    array_unshift($info,$offset);
    return $info;
}

?>
