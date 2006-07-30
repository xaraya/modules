<?php

/**
 * Adjust unix timestamp for timezone offset and daylight saving
 *
 * @param $args['timezone'] string the timezone we're looking for
 * @param $args['timestamp'] integer the timestamp we want to adjust
 * @return int adjusted timestamp
 */
function timezone_userapi_gettime($args=array())
{
    extract($args);
    if (empty($timestamp)) {
        $timestamp = time();
    }
    if (empty($timezone)) {
        return $timestamp;
    }

    // array(tz offset, dst start, dst end, dst adjust, std code, dst code)
    $info = xarModAPIFunc('timezone','user','tzinfo',
                          array('timezone'  => $timezone,
                                'timestamp' => $timestamp));
    if (empty($info)) {
        return $timestamp;
    }

    // check against DST start and end (in UTC)
    if ($info[1] < $info[2] && $timestamp >= $info[1] && $timestamp < $info[2]) {
        $timestamp += $info[3];
    } elseif ($info[1] > $info[2] && ($timestamp >= $info[1] || $timestamp < $info[2])) {
        $timestamp += $info[3];
    }

    // add timezone offset
    $timestamp += $info[0];

    return $timestamp;
}
?>