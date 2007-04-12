<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * the monitor processes administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_monitor_processes()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-monitor_processes.php

include_once(GALAXIA_LIBRARY.'/ProcessMonitor.php');

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

if (isset($_REQUEST['filter_active']) && $_REQUEST['filter_active'])
    $wheres[] = "isActive='" . $_REQUEST['filter_active'] . "'";

if (isset($_REQUEST['filter_valid']) && $_REQUEST['filter_valid'])
    $wheres[] = "isValid='" . $_REQUEST['filter_valid'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
    $wheres[] = "pId=" . $_REQUEST['filter_process'] . "";

$where = implode(' and ', $wheres);

if (!isset($_REQUEST["sort_mode"])) {
    $sort_mode = 'name_asc';
} else {
    $sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
    $offset = 1;
} else {
    $offset = $_REQUEST["offset"];
}

$tplData['offset'] =&  $offset;

if (isset($_REQUEST["find"])) {
    $find = $_REQUEST["find"];
} else {
    $find = '';
}

$tplData['find'] =  $find;
$tplData['where'] =  $where;
$tplData['sort_mode'] =&  $sort_mode;

$items = $processMonitor->monitor_list_processes($offset - 1, $maxRecords, $sort_mode, $find, $where);
$tplData['cant'] =  $items['cant'];

$cant_pages = ceil($items["cant"] / $maxRecords);
$tplData['cant_pages'] =&  $cant_pages;
$tplData['actual_page'] =  1 + (($offset - 1) / $maxRecords);

if ($items["cant"] >= ($offset + $maxRecords)) {
    $tplData['next_offset'] =  $offset + $maxRecords;
} else {
    $tplData['next_offset'] =  -1;
}

if ($offset > 1) {
    $tplData['prev_offset'] =  $offset - $maxRecords;
} else {
    $tplData['prev_offset'] =  -1;
}

$tplData['items'] =&  $items["data"];

$maxtime = 0;
foreach ($items['data'] as $info) {
    if (isset($info['duration']) && $maxtime < $info['duration']['max']) {
        $maxtime = $info['duration']['max'];
    }
}
if ($maxtime > 0) {
    $scale = 200.0 / $maxtime;
} else {
    $scale = 1.0;
}
foreach ($items['data'] as $index => $info) {
    if (isset($info['duration'])) {
        $items['data'][$index]['duration']['min'] = xarTimeToDHMS($info['duration']['min']);
        $items['data'][$index]['duration']['avg'] = xarTimeToDHMS($info['duration']['avg']);
        $items['data'][$index]['duration']['max'] = xarTimeToDHMS($info['duration']['max']);
        $info['duration']['max'] -= $info['duration']['avg'];
        $info['duration']['avg'] -= $info['duration']['min'];
        $items['data'][$index]['timescale'] = array();
        $items['data'][$index]['timescale']['max'] = intval( $scale * $info['duration']['max'] );
        $items['data'][$index]['timescale']['avg'] = intval( $scale * $info['duration']['avg'] );
        $items['data'][$index]['timescale']['min'] = intval( $scale * $info['duration']['min'] );
    }
}

$all_procs = $processMonitor->monitor_list_all_processes('name_asc');
$tplData['all_procs'] =&  $all_procs;

$sameurl_elements = array(
    'offset',
    'sort_mode',
    'where',
    'find',
    'filter_valid',
    'filter_process',
    'filter_active',
    'processId'
);

$tplData['stats'] =  $processMonitor->monitor_stats();

$tplData['mid'] =  'tiki-g-monitor_processes.tpl';

// Missing variables
$tplData['filter_process'] = isset($_REQUEST['filter_process']) ? $_REQUEST['filter_process'] : '';
$tplData['filter_active'] = isset($_REQUEST['filter_active']) ? $_REQUEST['filter_active'] : '';
$tplData['filter_valid'] = isset($_REQUEST['filter_valid']) ? $_REQUEST['filter_valid'] : '';

    $url = xarServerGetCurrentURL(array('offset' => '%%'));
    $tplData['pager'] = xarTplGetPager($tplData['offset'],
                                       $items['cant'],
                                       $url,
                                       $maxRecords);
    return $tplData;
}

?>
