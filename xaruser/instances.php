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
 * the instances user function
 *
 * @author mikespub
 * @access public
 */
function workflow_user_instances()
{
    // Security Check
    if (!xarSecurityCheck('ReadWorkflow')) return;

    if (isset($_REQUEST['run']) || isset($_REQUEST['run_x'])) {
        return xarModFunc('workflow','user','run_activity');
    }

    if (isset($_REQUEST['remove']) || isset($_REQUEST['remove_x'])) {
        xarVarFetch('iid','isset',$iid,'',XARVAR_NOT_REQUIRED);
        xarVarFetch('return_url','isset',$return_url,'',XARVAR_NOT_REQUIRED);
        if (!empty($iid)) {
            if (xarUserIsLoggedIn()) {
                $seenlist = xarModGetUserVar('workflow','seenlist');
                if (empty($seenlist)) {
                    xarModSetUserVar('workflow','seenlist',$iid);
                } else {
                    xarModSetUserVar('workflow','seenlist',$seenlist.';'.$iid);
                }
            } else {
                $seenlist = xarSessionGetVar('workflow.seenlist');
                if (empty($seenlist)) {
                    xarSessionSetVar('workflow.seenlist',$iid);
                } else {
                    xarSessionSetVar('workflow.seenlist',$seenlist.';'.$iid);
                }
            }
            if (!empty($return_url)) {
                xarResponseRedirect($return_url);
                return true;
            }
        }
    }

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-user_instances.php

include_once (GALAXIA_LIBRARY.'/GUI.php');

// Check if feature is enabled and permissions
if ($feature_workflow != 'y') {
    $tplData['msg'] =  xarML("This feature is disabled");

    return xarTplModule('workflow', 'user', 'error', $tplData);
}

if ($tiki_p_use_workflow != 'y') {
    $tplData['msg'] =  xarML("Permission denied");

    return xarTplModule('workflow', 'user', 'error', $tplData);
    die;
}

$action = 0;

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
if (isset($_REQUEST['send']) || isset($_REQUEST['send_x'])) {
    $GUI->gui_send_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
    $action = 1;
} elseif (isset($_REQUEST['abort']) || isset($_REQUEST['abort_x'])) {
    $GUI->gui_abort_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
    $action = 1;
} elseif (isset($_REQUEST['exception']) || isset($_REQUEST['exception_x'])) {
    $GUI->gui_exception_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
    $action = 1;
} elseif (isset($_REQUEST['resume']) || isset($_REQUEST['resume_x'])) {
    $GUI->gui_resume_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
    $action = 1;
} elseif (isset($_REQUEST['grab']) || isset($_REQUEST['grab_x'])) {
    $GUI->gui_grab_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
    $action = 1;
} elseif (isset($_REQUEST['release']) || isset($_REQUEST['release_x'])) {
    $GUI->gui_release_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
    $action = 1;
}

if ($action && !empty($_REQUEST['return_url'])) {
    xarResponseRedirect($_REQUEST['return_url']);
    return true;
}

$where = '';
$wheres = array();

if (isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'])
    $wheres[] = "gi.status='" . $_REQUEST['filter_status'] . "'";

if (isset($_REQUEST['filter_act_status']) && $_REQUEST['filter_act_status'])
    $wheres[] = "gia.status='" . $_REQUEST['filter_act_status'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
    $wheres[] = "gi.pId=" . $_REQUEST['filter_process'] . "";

if (isset($_REQUEST['filter_activity']) && $_REQUEST['filter_activity'])
    $wheres[] = "gia.activityId=" . $_REQUEST['filter_activity'] . "";

if (isset($_REQUEST['filter_user']) && $_REQUEST['filter_user'])
    $wheres[] = "gia.user='" . $_REQUEST['filter_user'] . "'";

if (isset($_REQUEST['filter_owner']) && $_REQUEST['filter_owner'])
    $wheres[] = "owner='" . $_REQUEST['filter_owner'] . "'";

$where = implode(' and ', $wheres);

if (!isset($_REQUEST["sort_mode"])) {
    $sort_mode = 'pId_asc, instanceId_asc';
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

$items = $GUI->gui_list_user_instances($user, $offset - 1, $maxRecords, $sort_mode, $find, $where);
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

foreach ($items['data'] as $index => $info) {
    if (!empty($info['started'])) {
        $items['data'][$index]['started'] = xarLocaleGetFormattedDate('medium',$info['started']) . ' '
                                            . xarLocaleGetFormattedTime('short',$info['started']);
    }
    $items['data'][$index]['ownerId'] = $info['owner'];
    if (!empty($info['owner']) &&
        is_numeric($info['owner'])) {
        $items['data'][$index]['owner'] = xarUserGetVar('name', $info['owner']);
    }
    if (!is_numeric($info['user'])) {
        $items['data'][$index]['userId'] = $info['user'];
        continue;
    }
    $role = xarModAPIFunc('roles','user','get',
                          array('uid' => $info['user']));
    if (!empty($role)) {
        $items['data'][$index]['userId'] = $role['uid'];
        $items['data'][$index]['user'] = $role['name'];
        $items['data'][$index]['login'] = $role['uname'];
    }
}
$tplData['items'] =&  $items["data"];

$processes = $GUI->gui_list_user_processes($user, 0, -1, 'procname_asc', '', '');
$tplData['all_procs'] =&  $processes['data'];

$all_statuses = array(
    'aborted',
    'active',
    'exception'
);

$tplData['statuses'] =  $all_statuses;

//$section = 'workflow';
//include_once ('tiki-section_options.php');

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

$tplData['mid'] =  'tiki-g-user_instances.tpl';

// Missing variables
$tplData['filter_process'] = isset($_REQUEST['filter_process']) ? $_REQUEST['filter_process'] : '';
$tplData['filter_status'] = isset($_REQUEST['filter_status']) ? $_REQUEST['filter_status'] : '';
$tplData['filter_act_status'] = isset($_REQUEST['filter_act_status']) ? $_REQUEST['filter_act_status'] : '';
$tplData['filter_user'] = isset($_REQUEST['filter_user']) ? $_REQUEST['filter_user'] : '';
$tplData['userId'] = $user;
$tplData['user'] = xarUserGetVar('name', $user);

    $tplData['feature_help'] = $feature_help;
    $tplData['direct_pagination'] = $direct_pagination;
    $url = xarServerGetCurrentURL(array('offset' => '%%'));
    $tplData['pager'] = xarTplGetPager($tplData['offset'],
                                       $items['cant'],
                                       $url,
                                       $maxRecords);
    return $tplData;
}

?>
