<?php

/**
 * the save process administration function
 * 
 * @author mikespub
 * @access public 
 */
function workflow_admin_save_process()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-save_process.php

include_once (GALAXIA_LIBRARY.'/ProcessManager.php');

if ($feature_workflow != 'y') {
    $tplData['msg'] =  xarML("This feature is disabled");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

if ($tiki_p_admin_workflow != 'y') {
    $tplData['msg'] =  xarML("Permission denied");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

// The galaxia process manager PHP script.

// Check if we are editing an existing process
// if so retrieve the process info and assign it.
if (!isset($_REQUEST['pid']))
    $_REQUEST['pid'] = 0;

header ('Content-type: text/xml');
echo ('<?xml version="1.0"?>');
$data = $processManager->serialize_process($_REQUEST['pid']);
echo $data;

// TODO: clean up properly
die;
}

?>
