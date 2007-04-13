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
 * the monitor workitems administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_monitor_workitems()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');

// Adapted from tiki-g-monitor_workitems.php
include_once (GALAXIA_LIBRARY.'/ProcessMonitor.php');

    if (!xarVarFetch('filter_process','int',$data['filter_process'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter_activity', 'str',$data['filter_activity'], '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter_user',  'str',$data['filter_user'],  '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter_instance',  'str',$data['filter_instance'],  '',XARVAR_NOT_REQUIRED)) return;

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

if (!empty($data['filter_instance'])) $wheres[] = "instanceId='" . $data['filter_instance'] . "'";
if (!empty($data['filter_process'])) $wheres[] = "gp.id='" . $data['filter_process'] . "'";
if (!empty($data['filter_activity'])) $wheres[] = "ga.activityId='" . $data['filter_activity'] . "'";
if (!empty($data['filter_user'])) $wheres[] = "user='" . $data['filter_user'] . "'";

$where = implode(' and ', $wheres);

if (!isset($_REQUEST["sort_mode"])) {
    $sort_mode = 'instanceId_asc, `itemId_asc';
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

$items = $processMonitor->monitor_list_workitems($offset - 1, $maxRecords, $sort_mode, $find, $where);
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

$maxtime = 0;
foreach ($items['data'] as $info) {
    if (isset($info['duration']) && $maxtime < $info['duration']) {
        $maxtime = $info['duration'];
    }
}
if ($maxtime > 0) {
    $scale = 100.0 / $maxtime;
} else {
    $scale = 1.0;
}
foreach ($items['data'] as $index => $info) {
    $items['data'][$index]['timescale'] = intval( $scale * $info['duration'] );
    $items['data'][$index]['duration'] = xarTimeToDHMS($info['duration']);
    if (!empty($info['started'])) {
        $items['data'][$index]['started'] = xarLocaleGetFormattedDate('medium',$info['started']) . ' '
                                            . xarLocaleGetFormattedTime('short',$info['started']);
    }
    if (!is_numeric($info['user'])) continue;
    $items['data'][$index]['user'] = xarUserGetVar('name',$info['user']);
}
$data['items'] =&  $items["data"];

$allprocs = $processMonitor->monitor_list_all_processes('name_asc');
$data['all_procs'] = array();
foreach ($allprocs as $row) {
    $data['all_procs'][] = array('id' => $row['id'], 'name' => $row['name'] . ' ' . $row['version']);
}

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process']) {
    $where = ' id=' . $_REQUEST['filter_process'];
} else {
    $where = '';
}

$all_acts = $processMonitor->monitor_list_all_activities('name_desc', $where);
$data['all_acts'] =&  $all_acts;

$sameurl_elements = array(
    'offset',
    'sort_mode',
    'where',
    'find',
    'filter_user',
    'filter_activity',
    'filter_process',
    'filter_instance',
    'processId',
    'filter_process'
);

$types = $processMonitor->monitor_list_activity_types();
$data['types'] =&  $types;

$data['stats'] =  $processMonitor->monitor_stats();

$users = $processMonitor->monitor_list_wi_users();
$data['users'] = array();
foreach (array_keys($users) as $index) {
    if (!is_numeric($users[$index])) {
        $data['users'][$index]['user'] = $users[$index];
        $data['users'][$index]['userId'] = $users[$index];
    } else {
        $data['users'][$index]['user'] = xarUserGetVar('name',$users[$index]);
        $data['users'][$index]['userId'] = $users[$index];
    }
}

$data['mid'] =  'tiki-g-monitor_workitems.tpl';



    $url = xarServerGetCurrentURL(array('offset' => '%%'));
    $data['pager'] = xarTplGetPager($data['offset'],
                                       $items['cant'],
                                       $url,
                                       $maxRecords);
    return $data;
}

?>
