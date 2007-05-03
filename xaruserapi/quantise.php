<?php

/**
 * Quantise a unix timestamp (down) to a whole number of minutes.
 * All seconds are dropped regardless of the quantisation level.
 */

function ievents_userapi_quantise($args)
{
    // Take the module quanta setting.
    static $global_quanta = NULL;
    if (!isset($global_quanta)) $global_quanta = xarModAPIfunc('ievents', 'user', 'params', array('name' => 'quanta'));

    extract($args);

    // Use the module setting if none has been passed in.
    if (!isset($quanta)) $quanta = $global_quanta;

    if (!is_numeric($time)) return $time;

    //$cur_minutes = (int)date('i', $time);

    /*if (!empty($quanta)) {
        $new_minutes = str_pad($quanta * floor($cur_minutes / $quanta), 2, '0', STR_PAD_LEFT);
    } else {
        $new_minutes = str_pad($cur_minutes, 2, '0', STR_PAD_LEFT);
    }*/

    list($year, $month, $day, $hour, $minute) = explode('-', date('Y-m-d-H-i', $time));

    if (!empty($quanta)) $minute = str_pad($quanta * floor($minute / $quanta), 2, '0', STR_PAD_LEFT);

    return strtotime("${year}-${month}-${day} ${hour}:${minute}:00");
}

?>