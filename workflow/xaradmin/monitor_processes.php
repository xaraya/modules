<?php

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

include_once(GALAXIA_DIR.'/ProcessMonitor.php');

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

if (isset($_REQUEST['filter_active']) && $_REQUEST['filter_active'])
	$wheres[] = "isActive='" . $_REQUEST['filter_active'] . "'";

if (isset($_REQUEST['filter_valid']) && $_REQUEST['filter_valid'])
	$wheres[] = "isValid='" . $_REQUEST['filter_valid'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "pId=" . $_REQUEST['filter_process'] . "";

$where = implode(' and ', $wheres);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'lastModif_desc';
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

$items = $processMonitor->monitor_list_processes($offset, $maxRecords, $sort_mode, $find, $where);
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
