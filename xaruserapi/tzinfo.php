<?php

/**
 * Get timezone information
 *
 * @param $args['timezone'] string the timezone we're looking for
 * @param $args['timestamp'] integer the time period we're interested in
 * @returns array array(tz offset, dst start, dst end, dst adjust, std code, dst code)
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

    $zone = xarModAPIFunc('timezone','user','findzone',
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
    if (strstr($format,'%s')) {
        if (!empty($info[3]) && $info[3] != '-') {
            $stdformat = strtr($format,array('%s' => $info[3]));
        } else {
            $stdformat = strtr($format,array('%s' => ''));
        }
        $info[3] = $stdformat;
        if (!empty($info[4]) && $info[4] != '-') {
            $dstformat = strtr($format,array('%s' => $info[4]));
        } else {
            $dstformat = strtr($format,array('%s' => ''));
        }
        $info[4] = $dstformat;
    } else {
        $info[3] = $format;
        $info[4] = $format;
    }
    array_unshift($info,$offset);
    return $info;
}

?>