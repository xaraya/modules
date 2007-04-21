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
 * the monitor instances administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_monitor_instances()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    $tplData = array();
$maxRecords = xarModGetVar('workflow','itemsperpage');
// Adapted from tiki-g-monitor_instances.php

include_once (GALAXIA_LIBRARY.'/processmonitor.php');

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

if (isset($_REQUEST['update'])) {
    foreach ($_REQUEST['update_status'] as $key => $val) {
        $processMonitor->update_instance_status($key, $val);
    }

    foreach ($_REQUEST['update_actstatus'] as $key => $val) {
        $parts = explode(':', $val);

        $processMonitor->update_instance_activity_status($key, $parts[1], $parts[0]);
    }
}

if (isset($_REQUEST['delete'])) {
    foreach (array_keys($_REQUEST['inst'])as $ins) {
        $processMonitor->remove_instance($ins);
    }
}

if (isset($_REQUEST['remove_aborted'])) {
    $processMonitor->remove_aborted();
}

if (isset($_REQUEST['remove_all'])) {
    $processMonitor->remove_all($_REQUEST['filter_process']);
}

if (isset($_REQUEST['sendInstance'])) {
    //activityId indicates the activity where the instance was
    //and we have to send it to some activity to be determined
    include_once (GALAXIA_LIBRARY.'/api/instance.php');

    $instance = new Instance();
    $instance->getInstance($_REQUEST['sendInstance']);
    // Do not add a workitem since the instance must be already completed!
    $instance->complete($_REQUEST['activityId'], true, false);
    unset ($instance);
}

if (isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'])
    $wheres[] = "gi.status='" . $_REQUEST['filter_status'] . "'";

if (isset($_REQUEST['filter_act_status']) && $_REQUEST['filter_act_status'])
    $wheres[] = "actstatus='" . $_REQUEST['filter_act_status'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
    $wheres[] = "gi.pId=" . $_REQUEST['filter_process'] . "";

if (isset($_REQUEST['filter_activity']) && $_REQUEST['filter_activity'])
    $wheres[] = "gia.activityId=" . $_REQUEST['filter_activity'] . "";

if (isset($_REQUEST['filter_user']) && $_REQUEST['filter_user'])
    $wheres[] = "user='" . $_REQUEST['filter_user'] . "'";

if (isset($_REQUEST['filter_owner']) && $_REQUEST['filter_owner'])
    $wheres[] = "owner='" . $_REQUEST['filter_owner'] . "'";

$where = implode(' and ', $wheres);

if (!isset($_REQUEST["sort_mode"])) {
    $sort_mode = 'instanceId_asc';
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

$items = $processMonitor->monitor_list_instances($offset - 1, $maxRecords, $sort_mode, $find, $where, array());
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

$maxtime = 0;
foreach ($items['data'] as $index => $info) {
    if (!empty($info['ended'])) {
        $diff = $info['ended'] - $info['started'];
    } else {
        $diff = time() - $info['started'];
    }
    if ($maxtime < $diff) {
        $maxtime = $diff;
    }
    $items['data'][$index]['duration'] = $diff;
}
if ($maxtime > 0) {
    $scale = 100.0 / $maxtime;
} else {
    $scale = 1.0;
}
foreach ($items['data'] as $index => $info) {
    $items['data'][$index]['timescale'] = intval( $scale * $info['duration'] );
    $items['data'][$index]['duration'] = xarModApiFunc('workflow','user','timetodhms',array('time'=>$info['duration']));
    if (!empty($info['started'])) {
        $items['data'][$index]['started'] = xarLocaleGetFormattedDate('medium',$info['started']) . ' '
                                            . xarLocaleGetFormattedTime('short',$info['started']);
    }
    if (is_numeric($info['user'])) {
        $items['data'][$index]['user'] = xarUserGetVar('name',$info['user']);
    }
    if (is_numeric($info['owner'])) {
        $items['data'][$index]['owner'] = xarUserGetVar('name',$info['owner']);
    }
}
$tplData['items'] =&  $items["data"];

$all_procs = $processMonitor->monitor_list_all_processes('name_asc');
$tplData['all_procs'] =&  $all_procs;

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process']) {
    $where = ' pId=' . $_REQUEST['filter_process'];
} else {
    $where = '';
}

$all_acts = $processMonitor->monitor_list_all_activities('name_desc', $where);
$tplData['all_acts'] =&  $all_acts;

$types = $processMonitor->monitor_list_activity_types();
$tplData['types'] =&  $types;

$tplData['stats'] =  $processMonitor->monitor_stats();

$all_statuses = array(
    'aborted',
    'active',
    'completed',
    'exception'
);

$tplData['all_statuses'] =  $all_statuses;

$sameurl_elements = array(
    'offset',
    'sort_mode',
    'where',
    'find',
    'filter_user',
    'filter_status',
    'filter_act_status',
    'filter_type',
    'processId',
    'filter_process',
    'filter_owner',
    'filter_activity'
);

$tplData['statuses'] =  $processMonitor->monitor_list_statuses();
$users = $processMonitor->monitor_list_users();
$tplData['users'] = array();
foreach (array_keys($users) as $index) {
    if (!is_numeric($users[$index])) {
        $tplData['users'][$index]['user'] = $users[$index];
        $tplData['users'][$index]['userId'] = $users[$index];
    } else {
        $tplData['users'][$index]['user'] = xarUserGetVar('name',$users[$index]);
        $tplData['users'][$index]['userId'] = $users[$index];
    }
}
$owners =  $processMonitor->monitor_list_owners();
$tplData['owners'] = array();
foreach (array_keys($owners) as $index) {
    if (!is_numeric($owners[$index])) {
        $tplData['owners'][$index]['user'] = $owners[$index];
        $tplData['owners'][$index]['userId'] = $owners[$index];
    } else {
        $tplData['owners'][$index]['user'] = xarUserGetVar('name',$owners[$index]);
        $tplData['owners'][$index]['userId'] = $owners[$index];
    }
}
$tplData['mid'] =  'tiki-g-monitor_instances.tpl';

// Missing variables
$tplData['filter_process'] = isset($_REQUEST['filter_process']) ? $_REQUEST['filter_process'] : '';
$tplData['filter_activity'] = isset($_REQUEST['filter_activity']) ? $_REQUEST['filter_activity'] : '';
$tplData['filter_status'] = isset($_REQUEST['filter_status']) ? $_REQUEST['filter_status'] : '';
$tplData['filter_act_status'] = isset($_REQUEST['filter_act_status']) ? $_REQUEST['filter_act_status'] : '';
$tplData['filter_user'] = isset($_REQUEST['filter_user']) ? $_REQUEST['filter_user'] : '';
$tplData['filter_owner'] = isset($_REQUEST['filter_owner']) ? $_REQUEST['filter_owner'] : '';

    $url = xarServerGetCurrentURL(array('offset' => '%%'));
    $tplData['pager'] = xarTplGetPager($tplData['offset'],
                                       $items['cant'],
                                       $url,
                                       $maxRecords);
    return $tplData;
}

?>
