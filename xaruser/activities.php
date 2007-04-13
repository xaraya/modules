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
 * the activities user function
 *
 * @author mikespub
 * @access public
 */
function workflow_user_activities()
{
    // Security Check
    if (!xarSecurityCheck('ReadWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-user_activities.php

include_once (GALAXIA_LIBRARY.'/GUI.php');

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

/*
if(isset($_REQUEST['filter_active'])&&$_REQUEST['filter_active']) $wheres[]="isActive='".$_REQUEST['filter_active']."'";
if(isset($_REQUEST['filter_valid'])&&$_REQUEST['filter_valid']) $wheres[]="isValid='".$_REQUEST['filter_valid']."'";
*/
if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
    $wheres[] = "gp.id=" . $_REQUEST['filter_process'] . "";

$where = implode(' and ', $wheres);

if (!isset($_REQUEST["sort_mode"])) {
    $sort_mode = 'id_asc, flowNum_asc';
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

$items = $GUI->gui_list_user_activities($user, $offset - 1, $maxRecords, $sort_mode, $find, $where);
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

$processes = $GUI->gui_list_user_processes($user, 0, -1, 'procname_asc', '', '');
$tplData['all_procs'] =&  $processes['data'];
if (count($tplData['all_procs']) == 1 && empty($_REQUEST['filter_process'])) {
    $_REQUEST['filter_process'] = $tplData['all_procs'][0]['id'];
}

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process']) {
    $actid2item = array();
    foreach (array_keys($tplData['items']) as $index) {
        $actid2item[$tplData['items'][$index]['activityId']] = $index;
    }
    foreach ($tplData['all_procs'] as $info) {
        if ($info['id'] == $_REQUEST['filter_process'] && !empty($info['normalized_name'])) {
            $graph = GALAXIA_PROCESSES."/" . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".png";
            $mapfile = GALAXIA_PROCESSES."/" . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".map";
            if (file_exists($graph) && file_exists($mapfile)) {
                $maplines = file($mapfile);
                $map = '';
                foreach ($maplines as $mapline) {
                    if (!preg_match('/activityId=(\d+)/',$mapline,$matches)) continue;
                    $actid = $matches[1];
                    if (!isset($actid2item[$actid])) continue;
                    $index = $actid2item[$actid];
                    $item = $tplData['items'][$index];
                    if ($item['instances'] > 0) {
                        $url = xarModURL('workflow','user','instances',
                                         array('filter_process' => $info['id']));
                        $mapline = preg_replace('/href=".*?activityId/', 'href="' . $url . '&amp;filter_activity', $mapline);
                        $map .= $mapline;
                    } elseif ($item['isInteractive'] == 'y' && ($item['type'] == 'start' || $item['type'] == 'standalone')) {
                        $url = xarModURL('workflow','user','run_activity');
                        $mapline = preg_replace('/href=".*?activityId/', 'href="' . $url . '&amp;activityId', $mapline);
                        $map .= $mapline;
                    }
                }
                $tplData['graph'] = $graph;
                $tplData['map'] = $map;
                $tplData['procname'] = $info['procname'];
            } else {
                $tplData['graph'] = '';
            }
            break;
        }
    }
}

//$section = 'workflow';
//include_once ('tiki-section_options.php');
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

$tplData['mid'] =  'tiki-g-user_activities.tpl';

// Missing variable
$tplData['filter_process'] = isset($_REQUEST['filter_process']) ? $_REQUEST['filter_process'] : '';

    $url = xarServerGetCurrentURL(array('offset' => '%%'));
    $tplData['pager'] = xarTplGetPager($tplData['offset'],
                                       $items['cant'],
                                       $url,
                                       $maxRecords);
    return $tplData;
}

?>
