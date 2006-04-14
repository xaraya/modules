<?php

/**
 * the processes administration function
 * 
 * @author mikespub
 * @access public 
 */
function workflow_admin_processes()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-admin_processes.php

include_once(GALAXIA_LIBRARY.'/ProcessManager.php');

// The galaxia process manager PHP script.
if ($feature_workflow != 'y') {
    $tplData['msg'] =  xarML("This feature is disabled");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

if ($tiki_p_admin_workflow != 'y') {
    $tplData['msg'] =  xarML("Permission denied");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

// Check if we are editing an existing process
// if so retrieve the process info and assign it.
if (!isset($_REQUEST['pid']))
    $_REQUEST['pid'] = 0;

if ($_REQUEST["pid"]) {
    $info = $processManager->get_process($_REQUEST["pid"]);
    $info['graph'] = GALAXIA_PROCESSES."/" . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".png";
} else {
    $info = array(
        'name' => '',
        'description' => '',
        'version' => '1.0',
        'isActive' => 'n',
        'pId' => 0
    );
}

$tplData['proc_info'] = $info;
$tplData['pid'] =  $_REQUEST['pid'];
$tplData['info'] =  $info;

//Check here for an uploaded process
if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
    // move the uploaded file to some temporary wf* file in cache/templates
    $tmpdir = xarCoreGetVarDirPath();
    $tmpdir .= '/cache/templates';
    $tmpfile = tempnam($tmpdir, 'wf');
    if (move_uploaded_file($_FILES['userfile1']['tmp_name'], $tmpfile) && file_exists($tmpfile)) {
        $fp = fopen($tmpfile, "rb");

    $data = '';
    $fhash = '';

    while (!feof($fp)) {
        $data .= fread($fp, 8192 * 16);
    }

    fclose ($fp);
    $size = $_FILES['userfile1']['size'];
    $name = $_FILES['userfile1']['name'];
    $type = $_FILES['userfile1']['type'];

    $process_data = $processManager->unserialize_process($data);

    if ($processManager->process_name_exists($process_data['name'], $process_data['version'])) {
        $tplData['msg'] =  xarML("The process name already exists");

        return xarTplModule('workflow', 'admin', 'error', $tplData);
    } else {
        $processManager->import_process($process_data);
    }
        unlink($tmpfile);
    }
}

if (isset($_REQUEST["delete"])) {
    foreach (array_keys($_REQUEST["process"])as $item) {
        $processManager->remove_process($item);
    }
}

if (isset($_REQUEST['newminor'])) {
    $processManager->new_process_version($_REQUEST['newminor']);
}

if (isset($_REQUEST['newmajor'])) {
    $processManager->new_process_version($_REQUEST['newmajor'], false);
}

if (isset($_REQUEST['save'])) {
    $vars = array(
        'name' => $_REQUEST['name'],
        'description' => $_REQUEST['description'],
        'version' => $_REQUEST['version'],
        'isActive' => 'n'
    );

    if ($processManager->process_name_exists($_REQUEST['name'], $_REQUEST['version']) && $_REQUEST['pid'] == 0) {
        $tplData['msg'] =  xarML("Process already exists");

        return xarTplModule('workflow', 'admin', 'error', $tplData);
    }

    if (isset($_REQUEST['isActive']) && $_REQUEST['isActive'] == 'on') {
        $vars['isActive'] = 'y';
    }

    $pid = $processManager->replace_process($_REQUEST['pid'], $vars);

    $valid = $activityManager->validate_process_activities($pid);

    if (!$valid) {
        $processManager->deactivate_process($pid);
    }

    $info = array(
        'name' => '',
        'description' => '',
        'version' => '1.0',
        'isActive' => 'n',
        'pId' => 0
    );

    $tplData['info'] =  $info;
}

$where = '';
$wheres = array();

if (isset($_REQUEST['filter'])) {
    if ($_REQUEST['filter_name']) {
        $wheres[] = " name='" . $_REQUEST['filter_name'] . "'";
    }

    if ($_REQUEST['filter_active']) {
        $wheres[] = " isActive='" . $_REQUEST['filter_active'] . "'";
    }

    $where = implode('and', $wheres);
}

if (isset($_REQUEST['where'])) {
    $where = $_REQUEST['where'];
}

if (!isset($_REQUEST["sort_mode"])) {
    $sort_mode = 'lastModif_desc';
} else {
    $sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
    $offset = 1;
} else {
    $offset = $_REQUEST["offset"];
}

$tplData['offset'] = $offset;

if (isset($_REQUEST["find"])) {
    $find = $_REQUEST["find"];
} else {
    $find = '';
}

$tplData['find'] =  $find;
$tplData['where'] =  $where;
$tplData['sort_mode'] = $sort_mode;

$items = $processManager->list_processes($offset - 1, $maxRecords, $sort_mode, $find, $where);
$tplData['cant'] =  $items['cant'];

$cant_pages = ceil($items["cant"] / $maxRecords);
$tplData['cant_pages'] =  $cant_pages;
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

$tplData['items'] =  $items["data"];

if ($_REQUEST['pid']) {
    $valid = $activityManager->validate_process_activities($_REQUEST['pid']);

    $errors = array();

    if (!$valid) {
        $processManager->deactivate_process($_REQUEST['pid']);

        $errors = $activityManager->get_error();
    }

    $tplData['errors'] =  $errors;
}

$sameurl_elements = array(
    'offset',
    'sort_mode',
    'where',
    'find',
    'filter_name',
    'filter_active'
);

$all_procs = $items = $processManager->list_processes(0, -1, 'name_desc', '', '');
$tplData['all_procs'] =  $all_procs['data'];

$tplData['mid'] =  'tiki-g-admin_processes.tpl';

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
