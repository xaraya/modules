<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
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


    // Initialize some stuff
    $user = xarUserGetVar('id');
    $maxRecords = xarModVars::get('workflow','items_per_page');

    if (isset($_REQUEST['run']) || isset($_REQUEST['run_x'])) {
        return xarModFunc('workflow','user','run_activity');
    }

    if (isset($_REQUEST['remove']) || isset($_REQUEST['remove_x'])) {
        xarVarFetch('iid','isset',$iid,'',XARVAR_NOT_REQUIRED);
        xarVarFetch('return_url','isset',$return_url,'',XARVAR_NOT_REQUIRED);
        if (!empty($iid)) {
            if (xarUserIsLoggedIn()) {
                $seenlist = xarModUserVars::get('workflow','seenlist');
                if (empty($seenlist)) {
                    xarModUserVars::set('workflow','seenlist',$iid);
                } else {
                    xarModUserVars::set('workflow','seenlist',$seenlist.';'.$iid);
                }
            } else {
                $seenlist = xarSession::getVar('workflow.seenlist');
                if (empty($seenlist)) {
                    xarSession::setVar('workflow.seenlist',$iid);
                } else {
                    xarSession::setVar('workflow.seenlist',$seenlist.';'.$iid);
                }
            }
            if (!empty($return_url)) {
                xarController::redirect($return_url);
                return true;
            }
        }
    }

// Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    $data = array();

// Adapted from tiki-g-user_instances.php

include_once (GALAXIA_LIBRARY.'/gui.php');

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
    xarController::redirect($_REQUEST['return_url']);
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

$data['offset'] =&  $offset;

if (isset($_REQUEST["find"])) {
    $find = $_REQUEST["find"];
} else {
    $find = '';
}

$data['find'] =  $find;
$data['where'] =  $where;
$data['sort_mode'] =&  $sort_mode;

$items = $GUI->gui_list_user_instances($user, $offset - 1, $maxRecords, $sort_mode, $find, $where);
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

$processes = $GUI->gui_list_user_processes($user, 0, -1, 'procname_asc', '', '');
$data['all_procs'] =&  $processes['data'];

$all_statuses = array(
    'aborted',
    'active',
    'exception'
);

$data['statuses'] =  $all_statuses;

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

$data['mid'] =  'tiki-g-user_instances.tpl';

// Missing variables
$data['filter_process'] = isset($_REQUEST['filter_process']) ? $_REQUEST['filter_process'] : '';
$data['filter_status'] = isset($_REQUEST['filter_status']) ? $_REQUEST['filter_status'] : '';
$data['filter_act_status'] = isset($_REQUEST['filter_act_status']) ? $_REQUEST['filter_act_status'] : '';
$data['filter_user'] = isset($_REQUEST['filter_user']) ? $_REQUEST['filter_user'] : '';
$data['userId'] = $user;
$data['user'] = xarUserGetVar('name', $user);

/*    $data['pager'] = xarTplGetPager($data['offset'],
                                       $items['cant'],
                                       $url,
                                       $maxRecords);*/
    $data['url'] = xarServer::getCurrentURL(array('offset' => '%%'));
    $data['maxRecords'] = $maxRecords;
    return $data;
}

?>
