<?php

/**
 * the monitor activities administration function
 * 
 * @author mikespub
 * @access public 
 */
function workflow_admin_monitor_activities()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-monitor_activities.php

include_once (GALAXIA_LIBRARY.'/ProcessMonitor.php');

if ($feature_workflow != 'y') {
	$tplData['msg'] =  xarML("This feature is disabled");

	return xarTplModule('workflow', 'monitor', 'error', $tplData);
	die;
}

if ($tiki_p_admin_workflow != 'y') {
	$tplData['msg'] =  xarML("Permission denied");

	return xarTplModule('workflow', 'monitor', 'error', $tplData);
	die;
}

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

if (isset($_REQUEST['filter_isInteractive']) && $_REQUEST['filter_isInteractive'])
	$wheres[] = "isInteractive='" . $_REQUEST['filter_isInteractive'] . "'";

if (isset($_REQUEST['filter_isAutoRouted']) && $_REQUEST['filter_isAutoRouted'])
	$wheres[] = "isAutoRouted='" . $_REQUEST['filter_isAutoRouted'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "pId=" . $_REQUEST['filter_process'] . "";

if (isset($_REQUEST['filter_activity']) && $_REQUEST['filter_activity'])
	$wheres[] = "activityId=" . $_REQUEST['filter_activity'] . "";

if (isset($_REQUEST['filter_type']) && $_REQUEST['filter_type'])
	$wheres[] = "type=" . $_REQUEST['filter_type'] . "";

$where = implode(' and ', $wheres);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'pId_asc, flowNum_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
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

$items = $processMonitor->monitor_list_activities($offset, $maxRecords, $sort_mode, $find, $where);
$tplData['cant'] =  $items['cant'];

$cant_pages = ceil($items["cant"] / $maxRecords);
$tplData['cant_pages'] =&  $cant_pages;
$tplData['actual_page'] =  1 + ($offset / $maxRecords);

if ($items["cant"] > ($offset + $maxRecords)) {
	$tplData['next_offset'] =  $offset + $maxRecords;
} else {
	$tplData['next_offset'] =  -1;
}

if ($offset > 0) {
	$tplData['prev_offset'] =  $offset - $maxRecords;
} else {
	$tplData['prev_offset'] =  -1;
}

$tplData['items'] =&  $items["data"];

$all_procs = $items = $processMonitor->monitor_list_processes(0, -1, 'name_desc', '', '');
$tplData['all_procs'] =&  $all_procs["data"];

$pid2name = array();
foreach ($tplData['all_procs'] as $info) {
    $pid2name[$info['pId']] = $info['name'];
}
foreach (array_keys($tplData['items']) as $index) {
    $pid = $tplData['items'][$index]['pId'];
    if (isset($pid2name[$pid])) {
        $tplData['items'][$index]['procname'] = $pid2name[$pid];
    } else {
        $tplData['items'][$index]['procname'] = '?';
    }
}

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process']) {
	$where = ' pId=' . $_REQUEST['filter_process'];
} else {
	$where = '';
}

$all_acts = $processMonitor->monitor_list_activities(0, -1, 'name_desc', '', $where);
$tplData['all_acts'] =&  $all_acts["data"];
$types = $processMonitor->monitor_list_activity_types();
$tplData['types'] =&  $types;

$tplData['stats'] =  $processMonitor->monitor_stats();
$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_isInteractive',
	'filter_isAutoRouted',
	'filter_activity',
	'filter_type',
	'processId',
	'filter_process'
);

$tplData['mid'] =  'tiki-g-monitor_activities.tpl';

// Missing variables
$tplData['filter_process'] = isset($_REQUEST['filter_process']) ? $_REQUEST['filter_process'] : '';
$tplData['filter_activity'] = isset($_REQUEST['filter_activity']) ? $_REQUEST['filter_activity'] : '';
$tplData['filter_type'] = isset($_REQUEST['filter_type']) ? $_REQUEST['filter_type'] : '';
$tplData['filter_isInteractive'] = isset($_REQUEST['filter_isInteractive']) ? $_REQUEST['filter_isInteractive'] : '';
$tplData['filter_isAutoRouted'] = isset($_REQUEST['filter_isAutoRouted']) ? $_REQUEST['filter_isAutoRouted'] : '';

    if (count($smarty->tplData) > 0) {
       foreach (array_keys($smarty->tplData) as $key) {
           $tplData[$key] = $smarty->tplData[$key];
       }
    }
    $tplData['feature_help'] = $feature_help;
    $tplData['direct_pagination'] = $direct_pagination;
    $url = xarServerGetCurrentURL(array('offset' => '%%'));
    $tplData['pager'] = xarTplGetPager($tplData['offset'],
                                       $url,
                                       $items['cant'],
                                       $maxRecords);
    return $tplData;
}

?>
