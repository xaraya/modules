<?php

/**
 * the activities administration function
 * 
 * @author mikespub
 * @access public 
 */
function workflow_admin_activities()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-admin_activities.php

include_once(GALAXIA_LIBRARY.'/ProcessManager.php');

// The galaxia activities manager PHP script.
if ($feature_workflow != 'y') {
    $tplData['msg'] =  xarML("This feature is disabled");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

if ($tiki_p_admin_workflow != 'y') {
    $tplData['msg'] =  xarML("Permission denied");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

if (!isset($_REQUEST['pid'])) {
    $tplData['msg'] =  xarML("No process indicated");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

$tplData['pid'] =  $_REQUEST['pid'];

$proc_info = $processManager->get_process($_REQUEST['pid']);
$proc_info['graph']=GALAXIA_PROCESSES."/".$proc_info['normalized_name']."/graph/".$proc_info['normalized_name'].".png";



// Retrieve activity info if we are editing, assign to 
// default values when creating a new activity
if (!isset($_REQUEST['activityId']))
    $_REQUEST['activityId'] = 0;

if ($_REQUEST["activityId"]) {
    $info = $activityManager->get_activity($_REQUEST['pid'], $_REQUEST["activityId"]);
} else {
    $info = array(
        'name' => '',
        'description' => '',
        'activityId' => 0,
        'isInteractive' => 'y',
        'isAutoRouted' => 'n',
        'type' => 'activity'
    );
}

$tplData['activityId'] =  $_REQUEST['activityId'];
$tplData['info'] =  $info;

// Remove a role from the activity
if (isset($_REQUEST['remove_role']) && $_REQUEST['activityId']) {
    $activityManager->remove_activity_role($_REQUEST['activityId'], $_REQUEST['remove_role']);
}

$role_to_add = 0;

// Add a role to the process
if (isset($_REQUEST['addrole'])) {
    $isInteractive = (isset($_REQUEST['isInteractive']) && $_REQUEST['isInteractive'] == 'on') ? 'y' : 'n';

    $isAutoRouted = (isset($_REQUEST['isAutoRouted']) && $_REQUEST['isAutoRouted'] == 'on') ? 'y' : 'n';
    $info = array(
        'name' => $_REQUEST['name'],
        'description' => $_REQUEST['description'],
        'activityId' => $_REQUEST['activityId'],
        'isInteractive' => $isInteractive,
        'isAutoRouted' => $isAutoRouted,
        'type' => $_REQUEST['act_type'],
    );

    $vars = array(
        'name' => $_REQUEST['rolename'],
        'description' => ''
    );

    if (isset($_REQUEST["userole"]) && $_REQUEST["userole"]) {
        if ($_REQUEST['activityId']) {
            $activityManager->add_activity_role($_REQUEST['activityId'], $_REQUEST["userole"]);
        }
    } else {
        $rid = $roleManager->replace_role($_REQUEST['pid'], 0, $vars);

        if ($_REQUEST['activityId']) {
            $activityManager->add_activity_role($_REQUEST['activityId'], $rid);
        }
    }
}

// Delete activities
if (isset($_REQUEST["delete_act"])) {
    foreach (array_keys($_REQUEST["activity"])as $item) {
        $activityManager->remove_activity($_REQUEST['pid'], $item);
    }
}

// If we are adding an activity then add it!
if (isset($_REQUEST['save_act'])) {
    $isInteractive = (isset($_REQUEST['isInteractive']) && $_REQUEST['isInteractive'] == 'on') ? 'y' : 'n';

    $isAutoRouted = (isset($_REQUEST['isAutoRouted']) && $_REQUEST['isAutoRouted'] == 'on') ? 'y' : 'n';
    $vars = array(
        'name' => $_REQUEST['name'],
        'description' => $_REQUEST['description'],
        'activityId' => $_REQUEST['activityId'],
        'isInteractive' => $isInteractive,
        'isAutoRouted' => $isAutoRouted,
        'type' => $_REQUEST['act_type'],
    );

    if ($activityManager->activity_name_exists($_REQUEST['pid'], $_REQUEST['name']) && $_REQUEST['activityId'] == 0) {
        $tplData['msg'] =  xarML("Activity name already exists");

        return xarTplModule('workflow', 'admin', 'error', $tplData);
    }

    $newaid = $activityManager->replace_activity($_REQUEST['pid'], $_REQUEST['activityId'], $vars);
    $rid = 0;

    if (isset($_REQUEST['userole']) && $_REQUEST['userole'])
        $rid = $_REQUEST['userole'];

    if (!empty($_REQUEST['rolename'])) {
        $vars = array(
            'name' => $_REQUEST['rolename'],
            'description' => ''
        );

        $rid = $roleManager->replace_role($_REQUEST['pid'], 0, $vars);
    }

    if ($rid) {
        $activityManager->add_activity_role($newaid, $rid);
    }

    $info = array(
        'name' => '',
        'description' => '',
        'activityId' => 0,
        'isInteractive' => 'y',
        'isAutoRouted' => 'n',
        'type' => 'activity'
    );

    $_REQUEST['activityId'] = 0;
    $tplData['info'] =  $info;
    // remove transitions
    $activityManager->remove_activity_transitions($_REQUEST['pid'], $newaid);

    if (isset($_REQUEST["add_tran_from"])) {
        foreach ($_REQUEST["add_tran_from"] as $actfrom) {
            $activityManager->add_transition($_REQUEST['pid'], $actfrom, $newaid);
        }
    }

    if (isset($_REQUEST["add_tran_to"])) {
        foreach ($_REQUEST["add_tran_to"] as $actto) {
            $activityManager->add_transition($_REQUEST['pid'], $newaid, $actto);
        }
    }
}

// Get all the process roles
$all_roles = $roleManager->list_roles($_REQUEST['pid'], 0, -1, 'name_asc', '');
$tplData['all_roles'] =&  $all_roles['data'];

// Get activity roles
if ($_REQUEST['activityId']) {
    $roles = $activityManager->get_activity_roles($_REQUEST['activityId']);
} else {
    $roles = array();
}

$tplData['roles'] =  $roles;

$where = '';

if (isset($_REQUEST['filter'])) {
    $wheres = array();

    if ($_REQUEST['filter_type']) {
        $wheres[] = " type='" . $_REQUEST['filter_type'] . "'";
    }

    if ($_REQUEST['filter_interactive']) {
        $wheres[] = " isInteractive='" . $_REQUEST['filter_interactive'] . "'";
    }

    if ($_REQUEST['filter_autoroute']) {
        $wheres[] = " isAutoRouted='" . $_REQUEST['filter_autoroute'] . "'";
    }

    $where = implode('and', $wheres);
}

if (!isset($_REQUEST['sort_mode']))
    $_REQUEST['sort_mode'] = 'flowNum_asc';

if (!isset($_REQUEST['find']))
    $_REQUEST['find'] = '';

if (!isset($_REQUEST['were']))
    $_REQUEST['where'] = $where;

$tplData['sort_mode'] =  $_REQUEST['sort_mode'];
$tplData['find'] =  $_REQUEST['find'];
$tplData['where'] =  $_REQUEST['where'];

// Transitions
if (isset($_REQUEST["delete_tran"])) {
    foreach (array_keys($_REQUEST["transition"])as $item) {
        $parts = explode("_", $item);

        $activityManager->remove_transition($parts[0], $parts[1]);
    }
}

if (isset($_REQUEST['add_trans'])) {
    $activityManager->add_transition($_REQUEST['pid'], $_REQUEST['actFromId'], $_REQUEST['actToId']);
}

if (isset($_REQUEST['filter_tran_name']) && $_REQUEST['filter_tran_name']) {
    $transitions = $activityManager->get_process_transitions($_REQUEST['pid'], $_REQUEST['filter_tran_name']);
} else {
    $transitions = $activityManager->get_process_transitions($_REQUEST['pid'], '');
}

if (!isset($_REQUEST['filter_tran_name']))
    $_REQUEST['filter_tran_name'] = '';

$tplData['filter_tran_name'] =  $_REQUEST['filter_tran_name'];
$tplData['transitions'] =&  $transitions;

$valid = $activityManager->validate_process_activities($_REQUEST['pid']);
$proc_info['isValid'] = $valid ? 'y' : 'n';

if ($valid && isset($_REQUEST['activate_proc'])) {
    $processManager->activate_process($_REQUEST['pid']);

    $proc_info['isActive'] = 'y';
}

if (isset($_REQUEST['deactivate_proc'])) {
    $processManager->deactivate_process($_REQUEST['pid']);

    $proc_info['isActive'] = 'n';
}

$tplData['proc_info'] =&  $proc_info;

$errors = array();

if (!$valid) {
    $errors = $activityManager->get_error();
}

$tplData['errors'] =  $errors;

//Now information for activities in this process
$activities = $activityManager->list_activities($_REQUEST['pid'], 0, -1, $_REQUEST['sort_mode'], $_REQUEST['find'], $where);

//Now check if the activity is or not part of a transition
if (isset($_REQUEST['activityId'])) {
    for ($i = 0; $i < count($activities["data"]); $i++) {
        $id = $activities["data"][$i]['activityId'];

        $activities["data"][$i]['to']
            = $activityManager->transition_exists($_REQUEST['pid'], $_REQUEST['activityId'], $id) ? 'y' : 'n';
        $activities["data"][$i]['from']
            = $activityManager->transition_exists($_REQUEST['pid'], $id, $_REQUEST['activityId']) ? 'y' : 'n';
    }
}

// Set activities
if (isset($_REQUEST["update_act"])) {
    for ($i = 0; $i < count($activities["data"]); $i++) {
        $id = $activities["data"][$i]['activityId'];

        if (isset($_REQUEST['activity_inter']["$id"])) {
            $activities["data"][$i]['isInteractive'] = 'y';

            $activityManager->set_interactivity($_REQUEST['pid'], $id, 'y');
        } else {
            $activities["data"][$i]['isInteractive'] = 'n';

            $activityManager->set_interactivity($_REQUEST['pid'], $id, 'n');
        }

        if (isset($_REQUEST['activity_route']["$id"])) {
            $activities["data"][$i]['isAutoRouted'] = 'y';

            $activityManager->set_autorouting($_REQUEST['pid'], $id, 'y');
        } else {
            $activities["data"][$i]['isAutoRouted'] = 'n';

            $activityManager->set_autorouting($_REQUEST['pid'], $id, 'n');
        }
    }
}

$tplData['items'] =&  $activities['data'];

$activityManager->build_process_graph($_REQUEST['pid']);

$tplData['mid'] =  'tiki-g-admin_activities.tpl';

    // unknown variables ?
    $tplData['where2'] = '';
    $tplData['find2'] = '';
    $tplData['sort_mode2'] = '';

    $tplData['feature_help'] = $feature_help;
    $tplData['direct_pagination'] = $direct_pagination;
    return $tplData;
}

?>
