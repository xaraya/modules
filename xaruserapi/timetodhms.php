<?php

function workflow_userapi_timetodhms($args)
{
    extract($args);
    if(!isset($format)) $format = '';

    if ($time > 24*60*60) {
        $days = intval($time / (24*60*60));
        $time = $time % (24*60*60);
    } else {
        $days = 0;
    }
    if ($time > 60*60) {
        $hours = intval($time / (60*60));
        $time = $time % (60*60);
    } else {
        $hours = 0;
    }
    if ($time > 60) {
        $minutes = intval($time / 60);
        $time = $time % 60;
    } else {
        $minutes = 0;
    }
    $seconds = intval($time);
    if (!empty($format)) {
        // decide on some format :-)
    } elseif (!empty($days)) {
        $out = xarML('#(1)d #(2)h #(3)m #(4)s', $days, $hours, $minutes, $seconds);
    } elseif (!empty($hours)) {
        $out = xarML('#(1)h #(2)m #(3)s', $hours, $minutes, $seconds);
    } elseif (!empty($minutes)) {
        $out = xarML('#(1)m #(2)s', $minutes, $seconds);
    } elseif (!empty($seconds)) {
        $out = xarML('#(1)s', $seconds);
    } else {
        $out = '';
    }
    return $out;
}
?>