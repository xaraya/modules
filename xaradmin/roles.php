<?php

/**
 * the roles administration function
 * 
 * @author mikespub
 * @access public 
 */
function workflow_admin_roles()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-admin_roles.php

include_once(GALAXIA_LIBRARY.'/ProcessManager.php');

// The galaxia roles manager PHP script.
if ($feature_workflow != 'y') {
    $tplData['msg'] =  xarML("This feature is disabled");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

if ($tiki_p_admin_workflow != 'y') {
    $tplData['msg'] =  xarML("Permission denied");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

    if (!xarVarFetch('pid','id',$pid)) return;
    if (empty($pid)) {
        $tplData['msg'] =  xarML("No process indicated");
        return xarTplModule('workflow', 'admin', 'error', $tplData);
    }
    $tplData['pid'] =  $pid;

    $proc_info = $processManager->get_process($pid);
    $proc_info['graph']=GALAXIA_PROCESSES."/".$proc_info['normalized_name']."/graph/".$proc_info['normalized_name'].".png";


    if (!xarVarFetch('roleId','id',$roleId,0,XARVAR_NOT_REQUIRED)) return;
    if ($roleId) {
        $info = $roleManager->get_role($pid, $roleId);
    } else {
        $info = array(
                      'name' => '',
                      'description' => '',
                      'roleId' => 0
                     );
    }
    
    $tplData['roleId'] =  $roleId;
    $tplData['info'] =  $info;
    
// Delete roles
if (isset($_REQUEST["delete"])) {
    foreach (array_keys($_REQUEST["role"])as $item) {
        $roleManager->remove_role($pid, $item);
    }
}

// If we are adding an activity then add it!
if (isset($_REQUEST['save'])) {
    $vars = array(
        'name' => $_REQUEST['name'],
        'description' => $_REQUEST['description'],
    );

    $roleManager->replace_role($pid, $roleId, $vars);

    $info = array(
        'name' => '',
        'description' => '',
        'roleId' => 0
    );

    $tplData['info'] =  $info;
}

// MAPIING
    if (!isset($_REQUEST['find_users'])) $_REQUEST['find_users'] = '';

    $tplData['find_users'] = $_REQUEST['find_users'];

    $numusers = xarModAPIFunc('roles','user','countall');
// don't show thousands of users here without filtering
    if ($numusers > 1000 && empty($_REQUEST['find_users'])) {
        $tplData['users'] = array();
    } else {
        if (!empty($_REQUEST['find_users'])) {
            $dbconn =& xarDBGetConn();
            $selection = " AND xar_name LIKE " . $dbconn->qstr('%'.$_REQUEST['find_users'].'%');
        } else {
            $selection = '';
        }
        $tplData['users'] = xarModAPIFunc('roles','user','getall',
                                          array('selection' => $selection,
                                                'order' => 'name'));
    }

    $tplData['groups'] = xarModAPIFunc('roles','user','getallgroups');

$roles = $roleManager->list_roles($pid, 0, -1, 'name_asc', '');
$tplData['roles'] =&  $roles['data'];

if (isset($_REQUEST["delete_map"])) {
    foreach (array_keys($_REQUEST["map"])as $item) {
        $parts = explode(':::', $item);

        $roleManager->remove_mapping($parts[0], $parts[1]);
    }
}

if (isset($_REQUEST['mapg'])) {
    if ($_REQUEST['op'] == 'add') {
                $users = xarModAPIFunc('roles','user','getusers',
                                       array('uid' => $_REQUEST['group']));
        foreach ($users as $a_user) {
            $roleManager->map_user_to_role($pid, $a_user['uid'], $_REQUEST['role']);
        }
    } else {
                $users = xarModAPIFunc('roles','user','getusers',
                                       array('uid' => $_REQUEST['group']));
        foreach ($users as $a_user) {
            $roleManager->remove_mapping($a_user['uid'], $_REQUEST['role']);
        }
    }
}

if (isset($_REQUEST['save_map'])) {
    if (isset($_REQUEST['user']) && isset($_REQUEST['role'])) {
        foreach ($_REQUEST['user'] as $a_user) {
            if (empty($a_user)) {
                $a_user = _XAR_ID_UNREGISTERED;
            }
            foreach ($_REQUEST['role'] as $role) {
                $roleManager->map_user_to_role($pid, $a_user, $role);
            }
        }
    }
}

// list mappings
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
$tplData['sort_mode'] =&  $sort_mode;
$mapitems = $roleManager->list_mappings($pid, $offset - 1, $maxRecords, $sort_mode, $find);

// trick : replace userid by user here !
foreach (array_keys($mapitems['data']) as $index) {
    $role = xarModAPIFunc('roles','user','get',
                          array('uid' => $mapitems['data'][$index]['user']));
    if (!empty($role)) {
        $mapitems['data'][$index]['userId'] = $role['uid'];
        $mapitems['data'][$index]['user'] = $role['name'];
        $mapitems['data'][$index]['login'] = $role['uname'];
    } else {
        $roleManager->remove_mapping($mapitems['data'][$index]['user'], $mapitems['data'][$index]['roleId']);
        $mapitems['cant'] = $mapitems['cant'] - 1;
        unset($mapitems['data'][$index]);
    }
}

$tplData['cant'] =  $mapitems['cant'];
$cant_pages = ceil($mapitems["cant"] / $maxRecords);
$tplData['cant_pages'] =&  $cant_pages;
$tplData['actual_page'] =  1 + (($offset - 1) / $maxRecords);

if ($mapitems["cant"] >= ($offset + $maxRecords)) {
    $tplData['next_offset'] =  $offset + $maxRecords;
} else {
    $tplData['next_offset'] =  -1;
}

if ($offset > 1) {
    $tplData['prev_offset'] =  $offset - $maxRecords;
} else {
    $tplData['prev_offset'] =  -1;
}

$tplData['mapitems'] =&  $mapitems["data"];

//MAPPING
if (!isset($_REQUEST['sort_mode2']))
    $_REQUEST['sort_mode2'] = 'name_asc';

$tplData['sort_mode2'] =  $_REQUEST['sort_mode2'];
// Get all the process roles
$all_roles = $roleManager->list_roles($pid, 0, -1, $_REQUEST['sort_mode2'], '');
$tplData['items'] =&  $all_roles['data'];

$valid = $activityManager->validate_process_activities($pid);
$proc_info['isValid'] = $valid ? 'y' : 'n';
$errors = array();

if (!$valid) {
    $errors = $activityManager->get_error();
}

$tplData['errors'] =  $errors;
$tplData['proc_info'] =  $proc_info;
$sameurl_elements = array(
    'offset',
    'sort_mode',
    'where',
    'find',
    'offset2',
    'find2',
    'sort_mode2',
    'where2',
    'processId'
);

$tplData['mid'] =  'tiki-g-admin_roles.tpl';

    $tplData['feature_help'] = $feature_help;
    $tplData['direct_pagination'] = $direct_pagination;
    $url = xarModURL('workflow','admin','roles',array('pid' => $tplData['pid'],'offset' => '%%'));
    $tplData['pager'] = xarTplGetPager($tplData['offset'],
                                       $mapitems['cant'],
                                       $url,
                                       $maxRecords);
    return $tplData;
}

?>
