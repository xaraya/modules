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
    $data = array();

// Adapted from tiki-g-monitor_processes.php
include_once(GALAXIA_LIBRARY.'/ProcessMonitor.php');

    if (!xarVarFetch('filter_process','int',$data['filter_process'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter_active', 'str',$data['filter_active'], '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter_valid',  'str',$data['filter_valid'],  '',XARVAR_NOT_REQUIRED)) return;

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

if (!empty($data['filter_active'])) $wheres[] = "isActive='" . $data['filter_active'] . "'";
if (!empty($data['filter_valid'])) $wheres[] = "isValid='" . $data['filter_valid'] . "'";
if (!empty($data['filter_process'])) $wheres[] = "id='" . $data['filter_process'] . "'";

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

$data['offset'] =&  $offset;

if (isset($_REQUEST["find"])) {
    $find = $_REQUEST["find"];
} else {
    $find = '';
}

$data['find'] =  $find;
$data['where'] =  $where;
$data['sort_mode'] =&  $sort_mode;

$items = $processMonitor->monitor_list_processes($offset - 1, $maxRecords, $sort_mode, $find, $where);
$data['cant'] =  $items['cant'];

$cant_pages = ceil($items["cant"] / $maxRecords);
$data['cant_pages'] =&  $cant_pages;
$data['actual_page'] =  1 + (($offset - 1) / $maxRecords);

if ($items["cant"] >= ($offset + $maxRecords)) {
    $data['next_offset'] =  $offset + $maxRecords;
} else {
    $data['next_offset'] =  -1;
}

if ($offset > 1) {
    $data['prev_offset'] =  $offset - $maxRecords;
} else {
    $data['prev_offset'] =  -1;
}

$data['items'] =&  $items["data"];

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

$allprocs = $processMonitor->monitor_list_all_processes('name_asc');
$data['all_procs'] = array();
foreach ($allprocs as $row) {
    $data['all_procs'][] = array('id' => $row['id'], 'name' => $row['name'] . ' ' . $row['version']);
}

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

$data['stats'] =  $processMonitor->monitor_stats();

$data['mid'] =  'tiki-g-monitor_processes.tpl';

    $url = xarServerGetCurrentURL(array('offset' => '%%'));
    $data['pager'] = xarTplGetPager($data['offset'],
                                       $items['cant'],
                                       $url,
                                       $maxRecords);
    return $data;
}

?>
