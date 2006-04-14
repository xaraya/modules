<?php
/**
 * Fake Tiki setup file so that TikiWiki modules can work under Xaraya
 */

// Configuration of the Galaxia Workflow Engine for Xaraya
include_once('modules/workflow/lib/Galaxia/config.php');

if (!function_exists('xarTimeToDHMS')) {
    function xarTimeToDHMS($time, $format = '')
    {
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
}

// Retrieve the current user
global $user;
$user = xarUserGetVar('uid');

// Some other variables used inside Galaxia
$feature_help = 'n';
$feature_workflow = 'y';

if (xarSecurityCheck('AdminWorkflow',0)) {
    $tiki_p_admin_workflow = 'y';
    $tiki_p_use_workflow = 'y';
} else {
    $tiki_p_admin_workflow = 'n';
    if (xarSecurityCheck('ReadWorkflow',0)) {
        $tiki_p_use_workflow = 'y';
    } else {
        $tiki_p_use_workflow = 'n';
    }
}
$maxRecords = xarModGetVar('workflow','itemsperpage');
if (empty($maxRecords)) {
    xarModSetVar('workflow','itemsperpage',20);
    $maxRecords = 20;
}
$direct_pagination = 'y';
?>
