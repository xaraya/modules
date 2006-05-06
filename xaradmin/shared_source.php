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
 * the shared source administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_shared_source()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-admin_shared_source.php

include_once(GALAXIA_LIBRARY.'/ProcessManager.php');

// The galaxia source editor for activities and
// processes.
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

if (isset($_REQUEST['code'])) {
    unset ($_REQUEST['template']);

    $_REQUEST['save'] = 'y';
}

$proc_info = $processManager->get_process($_REQUEST['pid']);
$proc_info['graph']=GALAXIA_PROCESSES."/".$proc_info['normalized_name']."/graph/".$proc_info['normalized_name'].".png";
$tplData['proc_info'] =& $proc_info;

$procname = $proc_info['normalized_name'];

$tplData['warn'] =  '';

if (!isset($_REQUEST['activityId']))
    $_REQUEST['activityId'] = 0;

$tplData['activityId'] =  $_REQUEST['activityId'];

if ($_REQUEST['activityId']) {
    $act_info = $activityManager->get_activity($_REQUEST['pid'], $_REQUEST['activityId']);

    $actname = $act_info['normalized_name'];

    if (isset($_REQUEST['template'])) {
        $tplData['template'] =  'y';

        $source = GALAXIA_PROCESSES."/$procname/code/templates/$actname" . '.tpl';
    } else {
        $tplData['template'] =  'n';

        $source = GALAXIA_PROCESSES."/$procname/code/activities/$actname" . '.php';
    }

    // Then editing an activity
    $tplData['act_info'] =  $act_info;
} else {
    $tplData['template'] =  'n';
    $tplData['act_info'] =  array('isInteractive' => 'n', 'type' => 'shared');
    // Then editing shared code
    $source = GALAXIA_PROCESSES."/$procname/code/shared.php";
}

//First of all save
if (isset($_REQUEST['source'])) {
    // security check on paths
    $basedir = GALAXIA_PROCESSES . "/$procname/code/";
    $basepath = realpath($basedir);
    $sourcepath = realpath($_REQUEST['source_name']);
    if (substr($sourcepath,0,strlen($basepath)) == $basepath) {
        $fp = fopen($_REQUEST['source_name'], "wb");

        if (get_magic_quotes_gpc()) {
            $_REQUEST['source'] = stripslashes($_REQUEST['source']);
        }
        fwrite($fp, $_REQUEST['source']);
        fclose ($fp);
        if ($_REQUEST['activityId']) {
            $activityManager->compile_activity($_REQUEST['pid'], $_REQUEST['activityId']);
        }
    } else {
        die('potential hack attack');
    }
}

$tplData['source_name'] =  $source;

$fp = fopen($source, "rb");
$tplData['data'] = '';
while (!feof($fp)) {
    $data = fread($fp, 4096);
    $tplData['data'] .=  htmlspecialchars($data);
}
fclose ($fp);

$valid = $activityManager->validate_process_activities($_REQUEST['pid']);
$errors = array();

if (!$valid) {
    $errors = $activityManager->get_error();

    $proc_info['isValid'] = 'n';
} else {
    $proc_info['isValid'] = 'y';
}

$tplData['errors'] =  $errors;

$activities = $activityManager->list_activities($_REQUEST['pid'], 0, -1, 'name_asc', '');
$tplData['items'] = $activities['data'];

$tplData['mid'] =  'tiki-g-admin_shared_source.tpl';

    $tplData['feature_help'] = $feature_help;
    $tplData['direct_pagination'] = $direct_pagination;
    return $tplData;
}

?>
