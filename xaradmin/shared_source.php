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
**/
sys::import('modules.workflow.lib.galaxia.api');
/**
 * the shared source administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_shared_source()
{
    // Security Check
    if (!xarSecurity::check('AdminWorkflow')) {
        return;
    }

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    $data = [];

    // Adapted from tiki-g-admin_shared_source.php

    include_once(GALAXIA_LIBRARY.'/processmanager.php');

    if (!isset($_REQUEST['pid'])) {
        $data['msg'] =  xarML("No process indicated");
        return xarTpl::module('workflow', 'admin', 'errors', $data);
    }

    $data['pid'] =  $_REQUEST['pid'];

    if (isset($_REQUEST['code'])) {
        unset($_REQUEST['template']);
        $_REQUEST['save'] = 'y';
    }

    $process = new \Galaxia\Api\Process($_REQUEST['pid']);
    $proc_info = $processManager->get_process($_REQUEST['pid']);
    $proc_info['graph']=$process->getGraph();
    $data['proc_info'] =& $proc_info;

    $procname = $process->getNormalizedName();

    $data['warn'] =  '';

    if (!isset($_REQUEST['activityId'])) {
        $_REQUEST['activityId'] = 0;
    }

    $data['activityId'] =  $_REQUEST['activityId'];

    if ($_REQUEST['activityId']) {
        $act = \Galaxia\Api\WorkflowActivity::get($_REQUEST['activityId']);

        $actname = $act->getNormalizedName();

        if (isset($_REQUEST['template'])) {
            $data['template'] =  1;

            $source = GALAXIA_PROCESSES."/$procname/code/templates/$actname" . '.xt';
        } else {
            $data['template'] =  0;

            $source = GALAXIA_PROCESSES."/$procname/code/activities/$actname" . '.php';
        }

        // Then editing an activity
        $data['act_info'] =  [
            'isInteractive' => $act->isInteractive() ? 1 : 0,
            'type'          => $act->getType(), ];
    } else {
        $data['template'] =  0;
        $data['act_info'] =  ['isInteractive' => 0, 'type' => 'shared'];
        // Then editing shared code
        $source = GALAXIA_PROCESSES."/$procname/code/shared.php";
    }

    //First of all save
    xarVar::fetch('source_data', 'str', $source_data, '', xarVar::NOT_REQUIRED);
    if (!empty($source_data)) {
        $source_data = htmlspecialchars_decode($source_data);
        //var_dump($source);exit;
        // security check on paths
        $basedir = GALAXIA_PROCESSES . "/$procname/code/";
        $basepath = realpath($basedir);
        $sourcepath = realpath($_REQUEST['source_name']);
        if (substr($sourcepath, 0, strlen($basepath)) == $basepath) {
            $fp = fopen($_REQUEST['source_name'], "w");
            fwrite($fp, $source_data);
            fclose($fp);
            if ($_REQUEST['activityId']) {
                $act = \Galaxia\Api\WorkflowActivity::get($_REQUEST['activityId']);
                $act->compile();
            }
        } else {
            die('potential hack attack');
        }
    }

    $data['source_name'] =  $source;

    $fp = fopen($source, "r");
    $data['data'] = '';
    while (!feof($fp)) {
        $filestring = fread($fp, 4096);
        $data['data'] .=  $filestring;
    }
    fclose($fp);

    // initialize template
    if (empty($data['data']) && isset($_REQUEST['template']) && !empty($act)) {
        $data['data'] = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
        $data['data'] .= "\n" . $act->name;
        $data['data'] .= "\n" . '</xar:template>';
    }

    $valid = $activityManager->validate_process_activities($_REQUEST['pid']);
    $errors = [];

    if (!$valid) {
        $errors = $activityManager->get_error();

        $proc_info['isValid'] = 0;
    } else {
        $proc_info['isValid'] = 1;
    }

    $data['errors'] =  $errors;

    $activities = $activityManager->list_activities($_REQUEST['pid'], 0, -1, 'name_asc', '');
    $data['items'] = $activities['data'];

    $data['mid'] =  'tiki-g-admin_shared_source.tpl';

    return $data;
}
